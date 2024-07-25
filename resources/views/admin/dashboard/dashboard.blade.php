@extends('admin.layouts.layout')
@section('content')
<div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
	<div class="subheader py-2 py-lg-4  subheader-solid " id="kt_subheader">
		<div class=" container-fluid  d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
			<div class="d-flex align-items-center flex-wrap mr-1">
				<div class="d-flex align-items-baseline flex-wrap mr-5">
					<h5 class="text-dark font-weight-bold my-1 mr-5">
						Dashboard </h5>
				</div>
			</div>
			@include("admin.elements.quick_links")
		</div>
	</div>
	<div class="d-flex flex-column-fluid">
		<div class=" container ">
			<div class="row">
				<div class="col-lg-12">
		
					<center><h1 >Coming Soon</h1></center>
				</div>
			</div>
		</div>
	</div>
</div>
@stop