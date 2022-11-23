<?php

class Buscador_Palabras_Silaba {

	public function __construct() {

    // Declaración de Variables
		$this->silaba = array(
		  'palabra' => null,         // (String) Palabra ingresada
		  'longitudPalabra' => null, // (int)    Longitud de la palabra
		  'numeroSilaba' => null,    // (int)    Número de sílabas de la palabra
		  'silabas' => null,         // (Array)  Array de objeto que contiene la sílaba (caracter) y la posicion en la palabra
		  'tonica' => null,          // (int)    Posición de la sílaba tónica (empieza en 1)
		  'letraTildada' => null,    // (int)    Posición de la letra tildada (si la hay)
		  'acentuacion' => null,     // (int)    Tipo acentuación de la palabra (Aguda, Grave, Esdrújula y Sobresdrújula)
		);

		$this->encontroTonica = null; // (bool)   Indica si se ha encontrado la sílaba tónica

	}

  /**
   * Devuelve Objeto 'silaba' con los valores calculados
   *
   * @param {string} palabra
   * @returns {Object}
   */
	public function getSilabas( $palabra ) {
    $this->posicionSilabas( $palabra );
    $this->acentuacion();

    return $this->silaba;
	}

  /**
   * Realiza cálculo de las sílabas
   *
   * @param {string} palabra
   * @returns {undefined}
   */
	public function posicionSilabas( $palabra ) {

    $this->silaba['palabra'] = strtolower(trim($palabra));
		$this->silaba['silabas'] = array();

		$this->silaba['longitudPalabra'] = mb_strlen($this->silaba['palabra']);
		$this->encontroTonica = false;
		$this->silaba['tonica'] = 0;
		$this->silaba['numeroSilaba'] = 0;
		$this->silaba['letraTildada'] = -1;

    // Variable que almacena sílaba y la posición de la sílaba
    $silabaAux;

    // Se recorre la palabra buscando las sílabas
    for ($actPos = 0; $actPos < $this->silaba['longitudPalabra'];) {
  
      $this->silaba['numeroSilaba']++;
      $silabaAux = array();
      $silabaAux['inicioPosicion'] = $actPos;

      // Las sílabas constan de tres partes: ataque, núcleo y coda
      $actPos = $this->ataque($this->silaba['palabra'], $actPos);
      $actPos = $this->nucleo($this->silaba['palabra'], $actPos);
      $actPos = $this->coda($this->silaba['palabra'], $actPos);

      // Obtiene y sílaba de la palabra
      $silabaAux['silaba'] = mb_substr($this->silaba['palabra'], $silabaAux['inicioPosicion'], $actPos - $silabaAux['inicioPosicion']);

      // Guarda sílaba de la palabra
      array_push($this->silaba['silabas'], $silabaAux);
      
      if (($this->encontroTonica) && ($this->silaba['tonica'] == 0))
        $this->silaba['tonica'] = $this->silaba['numeroSilaba']; // Marca la sílaba tónica
    }
    
    // Si no se ha encontrado la sílaba tónica (no hay tilde), se determina en base a
    // las reglas de acentuación
    if (!$this->encontroTonica) {
      if ($this->silaba['numeroSilaba'] < 2) {
        // Monosílabos
        $this->silaba['tonica'] = $this->silaba['numeroSilaba'];
      } else {
        // Polisílabos
        $letraFinal = $this->silaba['palabra'][$this->silaba['longitudPalabra'] - 1];
        $letraAnterior = $this->silaba['palabra'][$this->silaba['longitudPalabra'] - 2];

        if ((!$this->esConsonante($letraFinal) || ($letraFinal == 'y')) ||
            ((($letraFinal == 'n') || ($letraFinal == 's') && !$this->esConsonante($letraAnterior)))) {

            $this->silaba['tonica'] = $this->silaba['numeroSilaba'] - 1;	// Palabra llana

        } else {
            $this->silaba['tonica'] = $this->silaba['numeroSilaba'];		// Palabra aguda
        }
      }
    }

	}

  public function ataque($pal, $pos) {
    $chrArray = preg_split('//u', $pal, -1, PREG_SPLIT_NO_EMPTY);

    // Se considera que todas las consonantes iniciales forman parte del ataque
    $ultimaConsonante = 'a';
    while (($pos < $this->silaba['longitudPalabra']) && (($this->esConsonante($chrArray[$pos])) && ($chrArray[$pos] != 'y'))) {
        $ultimaConsonante = $chrArray[$pos];
        $pos++;
    }

    // (q | g) + u (ejemplo: queso, gueto)
    if ($pos < $this->silaba['longitudPalabra'] - 1)
        if ($chrArray[$pos] == 'u') {
            if ($ultimaConsonante == 'q')
                $pos++;
            else if ($ultimaConsonante == 'g') {
                $letra = $chrArray[$pos + 1];
                if (($letra == 'e') || ($letra == 'é') || ($letra == 'i') || ($letra == 'í'))
                    $pos++;
            }
        }
        else { // La u con diéresis se añade a la consonante
            if (($chrArray[$pos] === 'ü') || ($chrArray[$pos] == 'Ü'))
                if ($ultimaConsonante == 'g')
                    $pos++;
        }

    return $pos;
  }

  public function nucleo($pal, $pos) {
    $chrArray = preg_split('//u', $pal, -1, PREG_SPLIT_NO_EMPTY);

    // Sirve para saber el tipo de vocal anterior cuando hay dos seguidas
    $anterior = 0;
    $c;

    // 0 = fuerte
    // 1 = débil acentuada
    // 2 = débil

    if ($pos >= $this->silaba['longitudPalabra'])
        return $pos; // ¡¿No tiene núcleo?!

    // Se salta una 'y' al principio del núcleo, considerándola consonante
    if ($chrArray[$pos] == 'y')
        $pos++;

    // Primera vocal
    if ($pos < $this->silaba['longitudPalabra']) {
        $c = $chrArray[$pos];
        switch ($c) {
            // Vocal fuerte o débil acentuada
            case 'á':
            case 'Á':
            case 'à':
            case 'À':
            case 'é':
            case 'É':
            case 'è':
            case 'È':
            case 'ó':
            case 'Ó':
            case 'ò':
            case 'Ò':
                $this->silaba['letraTildada'] = $pos;
                $this->encontroTonica = true;
                $anterior = 0;
                $pos++;
                break;
            // Vocal fuerte
            case 'a':
            case 'A':
            case 'e':
            case 'E':
            case 'o':
            case 'O':
                $anterior = 0;
                $pos++;
                break;
            // Vocal dbil acentuada, rompe cualquier posible diptongo
            case 'í':
            case 'Í':
            case 'ì':
            case 'Ì':
            case 'ú':
            case 'Ú':
            case 'ù':
            case 'Ù':
            case 'ü':
            case 'Ü':
                $this->silaba['letraTildada'] = $pos;
                $anterior = 1;
                $pos++;
                $this->encontroTonica = true;
                return $pos;
                break;
            // Vocal dbil
            case 'i':
            case 'I':
            case 'u':
            case 'U':
                $anterior = 2;
                $pos++;
                break;
        }
    }

    // 'h' intercalada en el núcleo, no condiciona diptongos o hiatos
    $hache = false;
    if ($pos < $this->silaba['longitudPalabra']) {
        if ($chrArray[$pos] == 'h') {
            $pos++;
            $hache = true;
        }
    }

    // Segunda vocal
    if ($pos < $this->silaba['longitudPalabra']) {
        $c = $chrArray[$pos];
        switch ($c) {
            // Vocal fuerte o dbil acentuada
            case 'á':
            case 'Á':
            case 'à':
            case 'À':
            case 'é':
            case 'É':
            case 'è':
            case 'È':
            case 'ó':
            case 'Ó':
            case 'ò':
            case 'Ò':

                $this->silaba['letraTildada'] = $pos;
                if ($anterior != 0) {
                    $this->encontroTonica = true;
                }
                if ($anterior == 0) {    // Dos vocales fuertes no forman sílaba
                    if ($hache)
                        $pos--;
                    return $pos;
                }
                else {
                    $pos++;
                }

                break;
            // Vocal fuerte
            case 'a':
            case 'A':
            case 'e':
            case 'E':
            case 'o':
            case 'O':

                if ($anterior == 0) {    // Dos vocales fuertes no forman sílaba
                    if ($hache)
                        $pos--;
                    return $pos;
                }
                else {
                    $pos++;
                }

                break;

            // Vocal débil acentuada, no puede haber triptongo, pero si diptongo
            case 'í':
            case 'Í':
            case 'ì':
            case 'Ì':
            case 'ú':
            case 'Ú':
            case 'ù':
            case 'Ù':

                $this->silaba['letraTildada'] = $pos;

                if ($anterior != 0) {  // Se forma diptongo
                    $this->encontroTonica = true;
                    $pos++;
                }
                else if ($hache)
                    $pos--;

                return $pos;

                break;
            // Vocal débil
            case 'i':
            case 'I':
            case 'u':
            case 'U':
            case 'ü':
            case 'Ü':
                if ($pos < $this->silaba['longitudPalabra'] - 1) { // ¿Hay tercera vocal?
                    $siguiente = $chrArray[$pos + 1];
                    if (!$this->esConsonante($siguiente)) {
                        $letraAnterior = $chrArray[$pos - 1];
                        if ($letraAnterior == 'h')
                            $pos--;
                        return $pos;
                    }
                }

                // dos vocales débiles iguales no forman diptongo
                if ($chrArray[$pos] != $chrArray[$pos - 1])
                    $pos++;

                // Es un diptongo plano o descendente
                return $pos;
        }
    }

    // ¿tercera vocal?
    if ($pos < $this->silaba['longitudPalabra']) {
        $c = $chrArray[$pos];
        if (($c == 'i') || ($c == 'u')) { // Vocal débil
            $pos++;
            return $pos;  // Es un triptongo
        }
    }

    return $pos;
  }

  public function coda($pal, $pos) {
    $chrArray = preg_split('//u', $pal, -1, PREG_SPLIT_NO_EMPTY);

    if (($pos >= $this->silaba['longitudPalabra']) || (!$this->esConsonante($chrArray[$pos]))) {
        return $pos; // No hay coda
    } else {
        if ($pos == $this->silaba['longitudPalabra'] - 1) // Final de palabra
        {
            $pos++;
            return $pos;
        }

        // Si sólo hay una consonante entre vocales, pertenece a la siguiente sílaba
        if (!$this->esConsonante($chrArray[$pos + 1])) return $pos;

        $c1 = $chrArray[$pos];
        $c2 = $chrArray[$pos + 1];

        // ¿Existe posibilidad de una tercera consonante consecutiva?
        if (($pos < $this->silaba['longitudPalabra'] - 2)) {
            $c3 = $chrArray[$pos + 2];

            if (!$this->esConsonante($c3)) { // No hay tercera consonante
                // Los grupos ll, lh, ph, ch y rr comienzan sílaba

                if (($c1 == 'l') && ($c2 == 'l'))
                    return $pos;
                if (($c1 == 'c') && ($c2 == 'h'))
                    return $pos;
                if (($c1 == 'r') && ($c2 == 'r'))
                    return $pos;

                ///////// grupos nh, sh, rh, hl son ajenos al español(DPD)
                if (($c1 != 's') && ($c1 != 'r') &&
                    ($c2 == 'h'))
                    return $pos;

                // Si la y está precedida por s, l, r, n o c (consonantes alveolares),
                // una nueva sílaba empieza en la consonante previa, si no, empieza en la y
                if (($c2 == 'y')) {
                    if (($c1 == 's') || ($c1 == 'l') || ($c1 == 'r') || ($c1 == 'n') || ($c1 == 'c'))
                        return $pos;

                    $pos++;
                    return $pos;
                }

                // gkbvpft + l
                if (((($c1 == 'b') || ($c1 == 'v') || ($c1 == 'c') || ($c1 == 'k') ||
                        ($c1 == 'f') || ($c1 == 'g') || ($c1 == 'p') || ($c1 == 't')) &&
                        ($c2 == 'l')
                    )
                ) {
                    return $pos;
                }

                // gkdtbvpf + r

                if (((($c1 == 'b') || ($c1 == 'v') || ($c1 == 'c') || ($c1 == 'd') || ($c1 == 'k') ||
                        ($c1 == 'f') || ($c1 == 'g') || ($c1 == 'p') || ($c1 == 't')) &&
                        ($c2 == 'r')
                    )
                ) {
                    return $pos;
                }

                $pos++;
                return $pos;
            }
            else { // Hay tercera consonante
                if (($pos + 3) == $this->silaba['longitudPalabra']) { // Tres consonantes al final ¿palabras extranjeras?
                    if (($c2 == 'y')) { // 'y' funciona como vocal
                        if (($c1 == 's') || ($c1 == 'l') || ($c1 == 'r') || ($c1 == 'n') || ($c1 == 'c'))
                            return $pos;
                    }

                    if ($c3 == 'y') { // 'y' final funciona como vocal con c2
                        $pos++;
                    }
                    else {	// Tres consonantes al final ¿palabras extranjeras?
                        $pos += 3;
                    }
                    return $pos;
                }

                if (($c2 == 'y')) { // 'y' funciona como vocal
                    if (($c1 == 's') || ($c1 == 'l') || ($c1 == 'r') || ($c1 == 'n') || ($c1 == 'c'))
                        return $pos;

                    $pos++;
                    return $pos;
                }

                // Los grupos pt, ct, cn, ps, mn, gn, ft, pn, cz, tz, ts comienzan sílaba (Bezos)

                if (($c2 == 'p') && ($c3 == 't') ||
                    ($c2 == 'c') && ($c3 == 't') ||
                    ($c2 == 'c') && ($c3 == 'n') ||
                    ($c2 == 'p') && ($c3 == 's') ||
                    ($c2 == 'm') && ($c3 == 'n') ||
                    ($c2 == 'g') && ($c3 == 'n') ||
                    ($c2 == 'f') && ($c3 == 't') ||
                    ($c2 == 'p') && ($c3 == 'n') ||
                    ($c2 == 'c') && ($c3 == 'z') ||
                    ($c2 == 't') && ($c3 == 'z') ||
                    ($c2 == 't') && ($c3 == 's')) {
                    $pos++;
                    return $pos;
                }

                if (($c3 == 'l') || ($c3 == 'r') ||    // Los grupos consonánticos formados por una consonante
                    // seguida de 'l' o 'r' no pueden separarse y siempre inician
                    // sílaba
                    (($c2 == 'c') && ($c3 == 'h')) ||  // 'ch'
                    ($c3 == 'y')) {                   // 'y' funciona como vocal
                    $pos++;  // Siguiente sílaba empieza en c2
                }
                else
                    $pos += 2; // c3 inicia la siguiente sílaba
            }
        }
        else {
            if (($c2 == 'y')) return $pos;

            $pos += 2; // La palabra acaba con dos consonantes
        }
    }
    return $pos;
  }

  /**
   * Determina el tipo de acentuacion de la palabra
   *
   * @returns {undefined}
   */
  public function acentuacion() {
    switch ($this->silaba['numeroSilaba'] - $this->silaba['tonica']) {
      case 0:
        $this->silaba['acentuacion'] = 'Aguda';
        break;
      case 1:
        $this->silaba['acentuacion'] = 'Grave (Llana)';
        break;
      case 2:
        $this->silaba['acentuacion'] = 'Esdrújula';
        break;
      default:
        $this->silaba['acentuacion'] = 'Sobresdrújula';
        break;
    }
  }

  /**
   * Determina si c es una vocal fuerte o débil acentuada
   *
   * @param {string} c
   * @returns {boolean}
   */
  public function vocalFuerte($c) {
    switch ($c) {
      case 'a':
      case 'á':
      case 'A':
      case 'Á':
      case 'à':
      case 'À':
      case 'e':
      case 'é':
      case 'E':
      case 'É':
      case 'è':
      case 'È':
      case 'í':
      case 'Í':
      case 'ì':
      case 'Ì':
      case 'o':
      case 'ó':
      case 'O':
      case 'Ó':
      case 'ò':
      case 'Ò':
      case 'ú':
      case 'Ú':
      case 'ù':
      case 'Ù':
        return true;
    }
    return false;
  }

  /**
   * Determina si c no es una vocal
   *
   * @param {string} c
   * @returns {boolean}
   */
  public function esConsonante($c) {
    if (!$this->vocalFuerte($c)) {
      switch ($c) {
        // Vocal débil
        case 'i':
        case 'I':
        case 'u':
        case 'U':
        case 'ü':
        case 'Ü':
          return false;
      }
      return true;
    }
    return false;
  }

}
