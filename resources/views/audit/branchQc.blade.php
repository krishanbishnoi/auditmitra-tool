
    <div class="col-md-3 form-group">
        <label>Select Sub Products</label>
        {!! Form::select('sub_product[]', $subProducts, explode(',',$agency->sub_product_id), ['class' => 'form-control js-example-basic-single','placeholder'=>'Please Select Sub Products','multiple'=>'multiple','id'=>'sub_product','disabled' => 'disabled']) !!}
        </div>
    <div class="col-md-3 form-group">
        <label>Agency name</label>
        <input type="text" name="agency_name" class="form-control" value="{{$agency->name ?? ''}}" disabled>
    </div>
    {{-- <div class="col-md-3 form-group">
        <label>Agency Manager</label>
        <input  type="text" name="agency_manager" class="form-control" value="{{$agency->agency_manager ?? ''}}" data-id="{{$agency->user->id ?? ''}}" disabled>
        <input type="hidden" name="agency_manager_email" value="">
        <span id="agency_error" style="display:none"></span>
    </div> --}}
    {{-- <div class="col-md-3 form-group agency_error" style="display:none">
        <label>Other</label>
        <span id="agency_error" class="" style="display:none"></span>
    </div> --}}
    <div class="col-md-3 form-group">
        <label>Agency Owner</label>
        <input type="text" name="agency_manager" class="form-control" value="{{$agency->agency_manager ?? ''}}" disabled>
    </div>
    <!-- <div class="col-md-3 form-group">
        <label>Agency Phone</label>
        <input type="text" name="agency_phone" class="form-control" value="{{$agency->agency_phone ?? ''}}">
    </div> -->
    <div class="col-md-3 form-group">
    <label>Agency Mobile Number</label>
    {!! Form::select(
        'agency_phone', 
        $agency_mobile_number, 
        isset($manager_id->agency_mobile) ? $manager_id->agency_mobile : null, 
        ['class' => 'form-control', 'disabled' => 'disabled' ]
    ) !!}
</div>
<div class="col-md-3 form-group">
    <label>Agency Email</label>
    {!! Form::select(
        'agency_email', 
        $agency_email, 
        isset($manager_id->agency_email) ? $manager_id->agency_email : null, 
        ['id' => 'agency_mail', 'class' => 'form-control', 'readonly' => 'readonly']
    ) !!}
</div>

    <div class="col-md-3 form-group">
        <label>Agency Address</label>
        <input type="text" name="agency_address" class="form-control" value="{{$agency->address ?? ''}}" disabled>
    </div>



<div class="col-md-3 form-group">
    <label>City</label>
    <input type="text" name="city" class="form-control" value="{{$branchable->city->name ?? ''}}" disabled>
</div>
<div class="col-md-3 form-group">
    <label>location</label>
    <input type="text" name="location" class="form-control" value="{{$branchable->location ?? ''}}" disabled>
</div>
<div class="col-md-3 form-group">
    <label>Geo tag</label>
    <textarea name="geotag" id="demogeo" class="form-control" disabled>
        {{ $manager_id->latitude }} {{ $manager_id->longitude }}
    </textarea>
</div>
@php
    $myData=[];
    $myType=[];
    $code='';
    $bucket='';
    $i=0;
    foreach($branchable->branchable as $item){ 
        if($item->type=='Collection_Manager'){
            $myData[$item->type][]=$item;
        }
        else{
            $myData[$item->type]=$item;
        }  
    }
    $myType=array_keys($myData);
function getSortOrder($c) {
    $sortOrder = ['Collection_Manager','Area_Collection_Manager','Regional_Collection_Manager','Zonal_Collection_Manager','National_Collection_Manager','Group_Product_Head'];
    $pos = array_search($c, $sortOrder);
    return $pos !== false ? $pos : 99999;
}

function mysort($a, $b) {
    if( getSortOrder($a) < getSortOrder($b) ) {
        return -1;
    }elseif( getSortOrder($a) == getSortOrder($b) ) {
        return 0;
    }else {
        return 1;
    }
}
usort($myType, "mysort");
@endphp
<!-- 
@foreach($myType as $item)
    @if($item=='Collection_Manager')
    <div class="col-md-3 form-group">
        <label>{{str_replace('_',' ',$item)}}</label>

        <select class="form-control" name="{{$item}}" id="collection_manager-select" disabled>
            @foreach($myData[$item] as $value)
                @isset($value->user->employee_id)
                    
                
             <?php
                 if(!empty($userData)) {
                    
                    if($userData['id'] == $value->user->id){?>
                        <option data-code="{{$value->user->employee_id}}" data-bucket="{{$value->buckrt ?? ''}}" value="{{$value->user->id }}" selected>{{$value->user->name ?? ''}}</option>
                   <?php }
                   else{
                    ?>
                         <option data-code="{{$value->user->employee_id}}" data-bucket="{{$value->buckrt ?? ''}}" value="{{$value->user->id }}" >{{$value->user->name ?? ''}}</option>
                    <?php 
                   }
                 }else{?>
                       <option data-code="{{$value->user->employee_id}}" data-bucket="{{$value->buckrt ?? ''}}" value="{{$value->user->id }}">{{$value->user->name ?? ''}}</option>
                    <?php
                 }?>

                
               
                @php
                    if(count($myData[$item])==1){
                        $code=$value->user->employee_id ?? '';
                        $bucket=$value->bucket ?? '';
                    }
                @endphp

                @endisset
            @endforeach
        </select>
    </div>
    <div class="col-md-3 form-group">
        <label>{{str_replace('_',' ',$item)}} Emp Code</label>
        <input type="text" name="{{$item}}code" class="form-control" value="{{$code ?? ''}}" disabled>
    </div>
    <div class="col-md-3 form-group">
        <label>{{str_replace('_',' ',$item)}} bucket</label>
        <input type="text" name="{{$item}}_bucket" class="form-control" value="{{$bucket ?? ''}}" disabled>
    </div>
    @else
    <div class="col-md-3 form-group">
        <label>{{str_replace('_',' ',$item)}}</label>
        <input list="adventure" type="text" name="{{$item}}" class="form-control" value="{{$myData[$item]->user->name ?? ''}}" disabled>
    </div>
    @endif
@endforeach -->



