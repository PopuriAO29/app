<?php if($windowLastPage > 1): ?>
	<div class="pagination">
		<?php if( $windowFirstPage > 1 ): ?>
			<a href="<?= $pageTitle->getFullUrl( array( 'query' => $query, 'page' => ($windowFirstPage-1), 'crossWikia' => ( $isInterWiki ? '1' : '0' ), 'rankExpr' => $rankExpr ) ); ?>">...</a>
		<?php endif; ?>
		<?php for( $i = $windowFirstPage; $i <= $windowLastPage; $i++ ): ?>
		
			<?php if($i == $currentPage): ?>
				<?=$i;?>&nbsp;
			<?php else: ?>
				<a href="<?= $pageTitle->getFullUrl( array( 'query' => $query, 'page' => $i, 'crossWikia' => ( $isInterWiki ? '1' : '0' ), 'rankExpr' => $rankExpr ) ); ?>"><?=$i;?></a>&nbsp;
			<?php endif;?>
		<?php endfor; ?>
		
		<?php if( $windowLastPage < $pagesNum ): ?>
		<a href="<?= $pageTitle->getFullUrl( array( 'query' => $query, 'page' => $i, 'crossWikia' => ( $isInterWiki ? '1' : '0' ), 'rankExpr' => $rankExpr ) ); ?>">...</a>
		<?php endif; ?>
	</div>
<?php endif; ?>	