<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Buscador_Palabras
 * @subpackage Buscador_Palabras/public/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div id="buscador-palabras" class="bs5">
  <form class="buscador-palabras-form" name="buscador-palabras-form" method="get" action="">
    <div class="row g-3">
      <div class="col-6">
        <label class="form-label">Palabras que empiezan con:</label>
        <input type="text" class="form-control" name="i" value="<?= $this->buscador_palabras_params['i']; ?>">
      </div>
      <div class="col-6">
        <label class="form-label">Palabras que acaban con:</label>
        <input type="text" class="form-control" name="f" value="<?= $this->buscador_palabras_params['f']; ?>">
      </div>

      <div class="col-6">
        <label class="form-label">Palabras que tengan las letras:</label>
        <input type="text" class="form-control" name="ms" value="<?= $this->buscador_palabras_params['ms']; ?>">
      </div>
      <div class="col-6">
        <label class="form-label">Palabras que no tengan las letras:</label>
        <input type="text" class="form-control" name="mns" value="<?= $this->buscador_palabras_params['mns']; ?>">
      </div>

      <div class="col-6">
        <label class="form-label">Palabras que tengan la cadena:</label>
        <input type="text" class="form-control" name="m" value="<?= $this->buscador_palabras_params['m']; ?>">
      </div>
      <div class="col-6">
        <label class="form-label">Palabras que no tengan la cadena:</label>
        <input type="text" class="form-control" name="mn" value="<?= $this->buscador_palabras_params['mn']; ?>">
      </div>

      <div class="col-6">
        <label class="form-label">Número de sílabas:</label>
        <?php $silabas = array(
          0 => 'Cualquiera',
          1 => 1,
          2 => 2,
          3 => 3,
          4 => 4,
          5 => 5,
          6 => 6,
          7 => 7,
          8 => 8,
          9 => 9,
          10 => '10 o más',
        ); ?>
        <select class="form-select" name="fs">
        <?php foreach ($silabas as $id => $value) { ?>
          <option value="<?php echo $id; ?>" <?php echo ($id ==  $this->buscador_palabras_params['fs']) ? ' selected="selected"' : '';?>><?php echo $value;?></option>
        <?php } ?>
        </select>
      </div>
      <div class="col-6">
        <label class="form-label">Número de letras:</label>
        <?php $letras = array(
          0 => 'Cualquiera',
          2 => 2,
          3 => 3,
          4 => 4,
          5 => 5,
          6 => 6,
          7 => 7,
          8 => 8,
          9 => 9,
          100 => '10 a 14',
          150 => '15 o más',
        ); ?>
        <select class="form-select" name="fnl">
        <?php foreach ($letras as $id => $value) { ?>
          <option value="<?php echo $id; ?>" <?php echo ($id ==  $this->buscador_palabras_params['fnl']) ? ' selected="selected"' : '';?>><?php echo $value;?></option>
        <?php } ?>
        </select>
      </div>
      
      <div class="col-12">
        <label class="form-label">Filtro anti acentos</label>
        <?php $acentos = array(
          0 => 'Desactivado',
          2 => 'Sólo sin acentos',
          1 => 'Sólo con acentos',
        ); ?>
        <select class="form-select" name="fa">
        <?php foreach ($acentos as $id => $value) { ?>
          <option value="<?php echo $id; ?>" <?php echo ($id ==  $this->buscador_palabras_params['fa']) ? ' selected="selected"' : '';?>><?php echo $value;?></option>
        <?php } ?>
        </select>
      </div>
      
      <div class="col-12">
        <button type="submit" class="btn btn-primary">Buscar palabras</button>
      </div>
    </div>
  </form>

  <div class="h-100 p-4 rounded-3">
    <?php if ($this->isValid && !$this->palabrasLength) { ?>
      <h3>Lo sentimos, pero no se han encontrado palabras que:</h3>
    <?php } ?>
    
    <?php if ($this->isValid && $this->palabrasLength) { ?>
      <h4>Se han encontrado <?php echo $this->palabrasLength; ?> palabras que:</h4>
    <?php } ?>

    <ul>
      <?php if ($this->buscador_palabras_params['i']) { ?>
        <li>Palabras que empiezan con "<?php echo $this->buscador_palabras_params['i']; ?>".</li>
      <?php } ?>
      <?php if ($this->buscador_palabras_params['f']) { ?>
        <li>Terminen con la letra "<?php echo $this->buscador_palabras_params['f']; ?>".</li>
      <?php } ?>
      <?php if ($this->buscador_palabras_params['ms']) { ?>
        <li>Tengan las letras "<?php echo $this->buscador_palabras_params['ms']; ?>".</li>
      <?php } ?>
      <?php if ($this->buscador_palabras_params['mns']) { ?>
        <li>Palabras que no tengan las letras "<?php echo $this->buscador_palabras_params['mns']; ?>".</li>
      <?php } ?>
      <?php if ($this->buscador_palabras_params['m']) { ?>
        <li>Palabras que tengan la cadena "<?php echo $this->buscador_palabras_params['m']; ?>".</li>
      <?php } ?>
      <?php if ($this->buscador_palabras_params['mn']) { ?>
        <li>Palabras que no tengan la cadena "<?php echo $this->buscador_palabras_params['mn']; ?>".</li>
      <?php } ?>
      <?php if ($this->buscador_palabras_params['fs']) { ?>
        <li>Tengan <?php echo $this->buscador_palabras_params['fs']; ?> sílabas.</li>
      <?php } ?>
      <?php if ($this->buscador_palabras_params['fnl']) { ?>
        <li>Tengan <?php echo $this->buscador_palabras_params['fnl']; ?> letras.</li>
      <?php } ?>
      <?php if ($this->buscador_palabras_params['fa']) { ?>
        <li>Sólo las que <?php echo ($this->buscador_palabras_params['fa']) == 2 ? 'no ' : ''; ?>tengan acentos.</li>
      <?php } ?>

      <?php if ($this->isValid && $this->palabrasLength) { ?>
        <li>Usando el diccionario de español, con <?php echo $this->dicionarioLength; ?> palabras.</li>
      <?php } ?>
      <?php if(!$this->isValid) { ?>
        <li>Por favor, escribe algo en el buscador para que podamos ofrecerte algún resultado.</li>
      <?php }?>
    </ul>
  </div>

	<?php if($this->palabrasLength) { ?>
    <div class="buscador-palabras-resultados">
  		<?php foreach ($this->palabras as $key => $value) { ?>
  			<div class="buscador-palabras-grupo">
  				<h3 class="buscador-palabras-titulo">Palabras que tienen <?php echo $key; ?> letras</h3>

  				<?php foreach ($value as $k => $v) { ?>
  					<p class="buscador-palabras-texto"><?php echo $v; ?></p>
  				<?php } ?>
  			</div>
  		<?php } ?>
    </div>
	<?php } ?>
</div>
