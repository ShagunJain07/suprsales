{{-- It is for layout header, footer and sidebar --}}
@extends('layouts.app')
{{-- Main section is for the content when the working shows --}}
@section('content')
  {{-- It is for veryfy the customer and verified by their ids --}}
      @if ($message = Session::get('success'))
      <div class="alert alert-success">
        <p>{{ $message }}</p>
      </div>
      @endif

	@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
{{-- After verify the user it shoes iside the Assign Region --}}
<section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Assign Region</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
      {{-- It is the link of Home btn top right corner --}}
              <li class="breadcrumb-item"><a href="{{route('dashboard.index')}}">Home</a></li>
              <li class="breadcrumb-item active">Assign Region</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

	 <section class="content">
         {{-- It hows the checkbox of to show employess with no Assign region --}}
      <div class="container-fluid">
	         <div class="icheck-primary d-inline">
                <input type="checkbox" id="assigned_regions">
                 <label for="assigned_regions"></label>
             </div> List of Employees with No Regions
					  <hr>

	   <div class="card" id="non_assigned_user">

	     <div class="modal fade text-left assignregionModal">
           <div class="modal-dialog">
              <div class="modal-content" style="width: 600px; height: 100px;">
   {{-- It shows after clicking the Assign btn after selecting the non assign employees --}}
                <div class="card card-info">
              <div class="card-header">
                <h3 class="card-title">Assign Region</h3>
				 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
              </div>
          <input type="hidden" name="non_id" id="non_Id" class="form-control"  value="">
      {{-- instead of injecting a user's ID, you can inject the entire User model instance that matches the given ID. --}}
    <form class="form-horizontal" action="{{route('assignplant.store')}}" method="POST">
     {{-- This function can be used to generate the hidden input field in the HTML form --}}
            {{ csrf_field() }}

    <div class="card-body">
           <!-- This is the tag for the Employee Code as Employee ID -->
				<div class="form-group row">
                    <label  class="col-sm-4 col-form-label">Employee Code</label>
                  <div class="col-sm-8">
                   <input name="employees_id" id="employees_Id" class="form-control"  value="" readonly>
                  </div>
                </div>
                {{-- This is the tag for the Name of Employees  --> --}}
				  <div class="form-group row">
                    <label  class="col-sm-4 col-form-label">Name</label>
                    <div class="col-sm-8">
                   <input name="employees_name" id="employees_name" class="form-control"  value="" readonly>
                    </div>
                  </div>
  {{-- here  dropdown came with all REGION_NAME and after selecting REGION_ID will send to database --}}
		<div class="form-group row select2-teal">
          <label class="col-sm-4 col-form-label">Region</label>
          <div class="col-sm-8">
            <select class="select2" multiple="multiple" name="regional_id[]" id="regional_id" data-placeholder="Select Region" data-dropdown-css-class="select2-teal">
              @isset($plant)
				@foreach ($plant as $values)
				<option value="{{ $values['REGION_ID'] }}">{{ $values['REGION_NAME'] }}</option>
				@endforeach
			  @endisset
            </select>
          </div>
        </div>
    </div>
                <!-- /.card-body -->
     {{-- It submit the Assign region and update otherwise cancel btn use to cancle the edit --}}
            <div class="card-footer">
                  <button type="submit" class="btn btn-info float-right">Assign</button>
                  <button type="button" class="btn btn-info" data-dismiss="modal">Cancel</button>
            </div>

    </form>

            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>

					<div class="card-body">
{{-- It is for the data shown after clicking Assign btn in the non assign employees --}}
			  <table id="non_region" class="table table-bordered table-hover" >
			  <thead>


                  <tr>
                        <th>Employee Code</th>
                       <th>Name</th>
                        <th>Action</th>


                    </tr>
                  </thead>
                  <tbody>
 {{--  $details contain all EMP_ID with respect their name as EMP_NAME with REGION_ID and Region_Name used in the assignPlant.Blade.php --}}

				  @isset($details)
				  @foreach ($details as $values)
          {{-- It shows if there is no Resion assign with the EMP_IT then only it shows --}}
				 @if($values['REGION'] == null)
				  <tr>
					<td>{{ $values['EMP_ID'] }}</td>
					<td>
					{{ $values['EMP_NAME'] }}
					</td>
					<td>
					 @php
				$assign_region = json_encode($values);
				@endphp
                {{-- The value of region_Id define by the respective EMP_ID --}}
					   <a class='btn btn-info btn-sm assignregion' href='#' value="{{$assign_region}}" id="{{$values['EMP_ID']}}" title="Assign">
                             <i class="fas fa-map-marker-alt"></i><i class="fas fa-plus fa-xs" style="font-size: 0.5rem;"></i> Assign
                          </a>

			   </td>


				</tr>
				@endif
					  @endforeach
					   @endisset
       {{-- If The emp_ID and name is not found then it shows  --}}
			@empty($details)
			<script>toastr.warning("No records found.");</script>
			@endempty
</tbody>
					</table>

					</div>


              <!-- /.card-body -->
            </div>

{{-- It is for the edit btn inside ACtion br for Assign region users --}}
        <div class="row">
          <div class="col-12">
            <div class="card" id="assign_region">

				 <div class="modal fade text-left assignpltModal">
        <div class="modal-dialog">
          <div class="modal-content" style="width: 600px; height: 100px;">

                <div class="card card-warning">
              <div class="card-header">
                <h3 class="card-title">Update Employee Region</h3>
				 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
              </div>
          <input type="hidden" name="plt_id" id="plt_Id" class="form-control"  value="">

      <form class="form-horizontal" id="assign_plt_form" method="POST">
              {{-- This function can be used to generate the hidden input field in the HTML form --}}
             {{ csrf_field() }}
			  {{ method_field('PUT') }}
    {{-- This body is for the popup after clicking the Edit btn --}}
                <div class="card-body">
             <!-- This is the tag for the Employee Code as EMP_ID -->
				<div class="form-group row">
                    <label  class="col-sm-4 col-form-label">Employee Code</label>
                    <div class="col-sm-8">
                      <input class="form-control" id="Emp_ID" value="" readonly>
                    </div>
                  </div>
                   <!-- This is the tag for the Name -->
				  <div class="form-group row">
                    <label  class="col-sm-4 col-form-label">Name</label>
                    <div class="col-sm-8">
                      <input class="form-control" id="Emp_Name" value="" readonly>
                    </div>
                  </div>
    {{-- here  dropdown came with all REGION_NAME and after selecting REGION_ID will send to database --}}
		<div class="form-group row select2-teal">
          <label class="col-sm-4 col-form-label">Region</label>
          <div class="col-sm-8">
            <select class="select2" multiple="multiple" name="emply_id[]" id="emply_id" data-placeholder="Select Region" data-dropdown-css-class="select2-teal">
               @isset($plant)
				@foreach ($plant as $values)
				<option value="{{ $values['REGION_ID'] }}">{{ $values['REGION_NAME'] }}</option>
				@endforeach
			   @endisset
            </select>
          </div>
        </div>

                </div>
                <!-- /.card-body -->
                <div class="card-footer">
          {{-- TO submit the form to update the region --}}
                  <button type="submit" class="btn btn-warning float-right">Update</button>
                  <button type="button" class="btn btn-warning" data-dismiss="modal">Cancel</button>
                </div>

              </form>

            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>





			  <div class="card-body">

			  <table id="assign_plant" class="table table-bordered table-hover">
			  <thead>


                  <tr>
                        <th>Employee Code</th>
						<th>Name</th>
                       <th>Regions</th>
                        <th>Action</th>


                    </tr>
                  </thead>
                  <tbody>
				  @isset($details)
				  @foreach ($details as $value)
				  @if($value['REGION'] != null)
				  <tr>
					<td>{{ $value['EMP_ID'] }}</td>
					<td>{{ $value['EMP_NAME'] }}</td>
					<td>
					@foreach ($value['REGION'] as $proc_name)
          <span class='badge badge-secondary'>{{ $proc_name['REGION_NAME'] }} </span>
					@endforeach
					</td>
					<td>
					 @php
				$assign_plant = json_encode($value);
				@endphp

					   <a class='btn btn-warning btn-sm assignplant' href='#' value="{{$assign_plant}}" id="{{$value['EMP_ID']}}" title="Edit">
                             <i class='fas fa-edit'></i>
                          </a>

			   </td>


				</tr>
				@endif
					  @endforeach
					   @endisset
			@empty($details)
			<script>toastr.warning("No records found.");</script>
			@endempty
</tbody>
</table>

</div>


              <!-- /.card-body -->
            </div>
            <!-- /.card -->
            <!-- /.card -->

          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>

@endsection
