<div class="row">
	<div class="col-sm-12 col-md-5">
		<div class="dataTables_info">
			Showing <?php echo e($results->firstItem()); ?> to <?php echo e($results->lastItem()); ?> of <?php echo e($results->total()); ?> entries
		</div>
	</div>
	<div class="col-sm-12 col-md-7">
		<div class="d-flex justify-content-end align-items-center">
			<div class="dataTables_length mr-4">
				<label class="mb-0">
					Show
					<select name="per_page" class="class' => 'custom-select custom-select-sm form-control form-control-sm" onchange='page_limit()' id="per_page">
						<option Value="10" selected>Default</option>
						<option Value="15">15</option>
						<option Value="20">20</option>
						<option Value="30">30</option>
						<option Value="50">50</option>
						<option Value="100">100</option>
					</select>
				</label>
			</div>
			<?php
			$link_limit = 6;
			?>
			<?php if($results->lastPage() > 1): ?>
			<div class="dataTables_paginate paging_full_numbers">
				<ul class="pagination">
					<?php if($results->onFirstPage()): ?>
					<li class="paginate_button page-item previous disabled">
						<a href="javascript:void(0);" class="page-link">
							<i class="ki ki-arrow-back"></i>
						</a>
					</li>
					<?php else: ?>
					<li class="paginate_button page-item first">
						<a href="<?php echo e($results->url(1)); ?>" class="page-link">
							<i class="ki ki-double-arrow-back"></i>
						</a>
					</li>
					<li class="paginate_button page-item previous">
						<a href="<?php echo e($results->previousPageUrl()); ?>" class="page-link">
							<i class="ki ki-arrow-back"></i>
						</a>
					</li>
					<?php endif; ?>
					<?php for($i = 1; $i <= $results->lastPage(); $i++): ?>
						<?php
						$half_total_links = floor($link_limit / 2);
						$from = $results->currentPage() - $half_total_links;
						$to = $results->currentPage() + $half_total_links;
						if ($results->currentPage() < $half_total_links) {
							$to += $half_total_links - $results->currentPage();
						}
						if ($results->lastPage() - $results->currentPage() < $half_total_links) {
							$from -= $half_total_links - ($results->lastPage() - $results->currentPage()) - 1;
						}
						?>
						<?php if($from < $i && $i < $to): ?> <li class="paginate_button page-item <?php echo e(($results->currentPage() == $i) ? ' active' : ''); ?>">
							<a href='<?php echo e($results->url("$i")); ?>' class="page-link"><?php echo e($i); ?></a>
							</li>
							<?php endif; ?>
							<?php endfor; ?>
							<?php if($results->hasMorePages()): ?>
							<li class="paginate_button page-item next ">
								<a href="<?php echo e($results->nextPageUrl()); ?>" class="page-link">
									<i class="ki ki-arrow-next"></i>
								</a>
							</li>
							<li class="paginate_button page-item last">
								<a href="<?php echo e($results->url($results->lastPage())); ?>" class="page-link">
									<i class="ki ki-double-arrow-next"></i>
								</a>
							</li>
							<?php else: ?>
							<li class="paginate_button page-item next disabled">
								<a href="#" class="page-link">
									<i class="ki ki-arrow-next"></i>
								</a>
							</li>
							<?php endif; ?>
				</ul>
			</div>
			<?php endif; ?>
		</div>
	</div>
</div><?php /**PATH /home/zaz/web/zaz.dev.obdemo.com/public_html/resources/views/pagination/default.blade.php ENDPATH**/ ?>