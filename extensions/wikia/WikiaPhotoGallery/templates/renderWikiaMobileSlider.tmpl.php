<nav class="wkSlider imgs<?= count($images)?> images<?= (count($images) % 2 == 0) ? 'Even' : 'Odd' ?> "><?
	foreach ( $images as $i => $image ) {
		$mediaInfo = $image['mediaInfo'];
		$mediaInfo['attributes']['class'] = 'getThumb';

		?><?= F::app()->renderView( 'WikiaMobileMediaService', 'renderMedia', array(
		'class' => "img{$i}",
		'linked' => !empty( $image['imageLink'] ),
		'anchorAttributes' => ( !empty( $image['imageLink'] ) ) ? array( 'href' => $image['imageLink'] ) : null,
		'caption' => ( !empty( $image['imageTitle'] ) ) ? $image['imageTitle'] : null,
		'attributes' => $mediaInfo['attributes'],
		'parameters' => $mediaInfo['parameters'],
		'noscript' => $mediaInfo['noscript']
	)) ;?><?
	}
?></nav>
