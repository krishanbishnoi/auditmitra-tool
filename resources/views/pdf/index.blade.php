<html>



<title>Settalment</title>



<head>



  <style>



    @media print {



      body{



        /* width: 21cm; */



        /* height: 29.7cm; */



      } 



    }



    body {



      margin: 0 auto;



      padding: 0;



      font:normal 14px Arial, Helvetica, sans-serif;color:hsl(0, 0%, 29%);



    }



    .list-td span{padding: 5px 10px;}



    .exceptional tr:nth-child(even) {



      background-color: #f2f2f2;



    }



    .pageA4 {



      /*height: 297mm;*/



      margin:auto;



    }



    .list{



      width: 100%;



      display: table;



      table-layout: fixed;



      border-collapse: collapse;



    }



    .active{    color: #ffffff;    background: #c55a11; border-radius: 12px;}



    .list-td {



      display: table-cell;



      text-align:center;



      vertical-align: middle;



      word-wrap: break-word;



    }



    table{border-collapse: collapse;}



    table thead th{background:#1d54c3;color:#fff;}



    td,th{text-align:center;padding:10px; font-size: 14px;}



    td h2{color:#930e29;}



    .red{color:#930e29;}



    .footer{



      bottom: 0;



      width: 100%;



      left: 0;



      right: 0;



    }



    .page-break {



      page-break-after: always;



    }



  </style>



</head>



<body style="background: #fafafa; padding: 50px;">



  <!--First Section-->







  <div style="height:auto;position: relative; padding:50px; margin-bottom: 50px; background: #fff;">



    <div style="text-align:center;">



      <img src="{{ URL::asset('pdfImage/background.jpg') }}"  style="width:100%;">



    </div>



    <div  class="cls_002" style="position: absolute;      right: 50%;      margin: auto;      left: 50%;      width: 430px;      font-size: 26px;      bottom: 125px;      color: #fff;">



      <span class="cls_002">{{$state->name ?? ''}} - Score CardRBL</span>



    </div>



  </div>



  <div class="page-break"></div>





  <!--Second Section-->



  @php



  $chunkdata=$branch->chunk(3);



  @endphp



  @foreach ($chunkdata as $chunk)



  <div class="pageA4" style="position: relative;padding-bottom:45px;margin-bottom: 28px; background:#fff; margin-bottom: 50px; padding:50px;">



    <table class="exceptional"  width="100%">



      <tr>



        <td align='left'><h2 style="margin:0px;text-align:left;">{{$state->name ?? ''}} – Branches Covered



        </h2>



      </td>



      <td align="right"><img src="{{URL::asset('pdfImage/logo.jpg')}}" style=" vertical-align:bottom; width:150px;    margin-left: auto;              display: block;" /></td>



    </tr>



  </table>



  <hr/>

  <table  class="list exceptional" width="" style="width:100%; border-spacing: 0px; text-align: center;" >



    <tr>



      <td class="list-td" style="padding: 20px;" colspan="2"><img src="{{URL::asset('pdfImage/state_map_india/'.($state->name ?? '').'.jpg')}}" alt="" style="display: block; margin: auto; height: 500px; max-width: 100%;"/></td>



      <td class="list-td" style="padding: 20px;" colspan="2">  



        <table  style="width: 100%;">



          <tbody>



            @foreach ($chunk as $item)



            <tr >



              <!--<td style="width: 100px; margin: auto; text-align: center; vertical-align: top;" colspan="1"></td>-->



              <td colspan="12" style="padding: 0px;">



                <h3 style="display:block; margin-top:30px; font-weight: 100; text-align: left" colspan="1"><img src="{{URL::asset('pdfImage/location.svg')}}" class="" alt="" style="height: 24px; "/>{{$item->city->name??''}}</h3>



                <div class="card-body" style="background:#dae3f3; padding:10px 20px; margin-bottom: 20px; text-align: left;">



                  <p>  



                    <strong>{{$item->city->name ?? ''}} Head Office:</strong><br/>



                    {{$item->location ?? ''}}



                  </p>



                </div>



              </td>



            </tr>



            @endforeach



          </tbody>



        </table>



      </td>



    </tr>



  </table>



  <div class="footer">



    <div style="width:33%;display:inline-block;">

      <img src="{{URL::asset('pdfImage/logo-qdegrees.png')}}" style="height: 38px;">

    </div>



    <div style="width:33%;display:inline-block;text-align:center;">



      <div><img src="{{URL::asset('pdfImage/footer-middle.jpg')}}" style="width: 100px;"></div>



    </div>



  </div>



</div>



@endforeach



<!--Third Section-->







@foreach ($branch as $value)



<div class="page-break"></div>



<div class="pageA4" style="position: relative;padding-bottom:45px;margin-bottom: 28px; background:#fff; margin-bottom: 50px; padding:50px;">



  <table  width="100%">



    <tr>



      <td align='left'><h2 style="margin:0px;text-align:left;">IDFC First - {{$state->name ?? ''}} Branch & Agency</h2>



      </td>



      <td align="right"><img src="{{URL::asset('pdfImage/logo.jpg')}}" style=" vertical-align:bottom; width:150px;    margin-left: auto;              display: block;" /></td>



    </tr>



  </table>



  <hr />



  <table  class="" width="" style="width:100%; border-spacing: 0px; text-align: center; width: 100%;" >



    <thead style=" background:#c55a11;">



      <tr >



        <th class="" style="padding: 20px; background:#c55a11;" colspan="100">{{$state->name ?? ''}} Branch & Agency</th>



      </tr>



    </thead>



    <tbody>



      <tr>



        <td class="list-td" style="padding: 20px; text-align: left;" > 



          @foreach ($branch as $item)



          <span class="{{($item->name==$value->name)?'active':''}}">{{$item->name ?? ''}}</span>  



          @endforeach



        </td>



      </tr>



    </tbody>



  </table>



  <table width="100%" class="list">



    <tr>



      <td width="40%" class="list-td">



        <img src="{{ URL::asset('pdfImage/state_image/'.($state->name ?? '').'.png') }}" alt="" style="display: block; margin: auto; max-width: 100%;"/>



      </td>



      <td class="list-td" width="60%">



        <table  width="100%">



          <tr style="background:#c55a11;">



            <th style="color:#fff;" colspan="2">{{ $value->name ?? ''}} Branch & Agency – Wave - IV – Overview</th>



          </tr>



          <tr>



            <td class="red" style="width:50%;text-align:left;">Start Date: </td>



            <td style="width:50%"> {{($date[$value->id]['start']!=null)?$date[$value->id]['start']->format('jS M, Y'):''}}</td>



          </tr>



          <tr>



            <td class="red" style="width:50%;text-align:left;">End Date: </td>



            <td style="width:50%"> {{($date[$value->id]['end']!=null)?$date[$value->id]['end']->format('jS M, Y'):''}}</td>



          </tr>



        </table>



        <table  width="100%">



          <tr style="background:#c55a11;">



            <th style="color:#fff;" colspan="2">Number of Collection Managers and Agency </th>



          </tr>



          <tr>



            <td class="red" style="width:50%;text-align:left;">Collection Manager: </td>



            <td style="width:50%">{{($collectioncount[$value->id]>9)?$collectioncount[$value->id]:'0'.$collectioncount[$value->id]}}



            </td>



          </tr>



          <tr>



            <td class="red" style="width:50%;text-align:left;">Collection Agency:  </td>



            <td style="width:50%"> {{($agencycount[$value->id]>9)?$agencycount[$value->id]:'0'.$agencycount[$value->id]}}



            </td>



          </tr>



          <tr>



            <td class="red" style="width:50%;text-align:left;">Repo and Yard Agency:  </td>



            @php



            $totalrepoYard=$branchrepocount[$value->id]+$agencyrepocount[$value->id]+$yardcount[$value->id];



            @endphp



            <td style="width:50%"> {{($totalrepoYard>9)?$totalrepoYard:'0'.$totalrepoYard}}



            </td>



          </tr>



        </table>



        <table  width="100%">



          <tr style="background:#c55a11;">



            <th style="color:#fff;" colspan="2">Products Covered </th>



          </tr>



          <tr>



            <td  style="width:50%;text-align:center;background:#d4d4d4;color:#000;">Branch  </td>



            <td style="width:50%;background:#9dc3e6;text-align:center;color:#000;">Agency



            </td>



          </tr>



          <tr>

            <td  style="width:50%;" align="center"> 



              @foreach ($product[$value->id] as $pro)



              @php



              $productname='';



              $productname=str_replace('/','',($product_name[$pro]));      



              $productname=str_replace(' ','',($productname));      



              @endphp



              {{-- <span style="display: inline-block;width: 60px;text-align: center;



              height: 60px;"> --}}



              <span style="display: inline-block;text-align: center;">



                @if(file_exists('pdfImage/product_logo/'.$productname.'.png'))



                <img src="{{URL::asset('pdfImage/product_logo/'.$productname.'.png') }}" style="width: 30px;" alt="{{$productname}}">



                @else



                {{$productname}}



                @endif



              </span>



              @endforeach



            </td>



            <td  style="width:50%;" align="center"> 



              @foreach ($productAgency[$value->id] as $pro)



              @if($pro!=null)



              @php



              $productname='';



              $productname=str_replace('/','',($product_name[$pro]));      



              $productname=str_replace(' ','',($productname));      



              @endphp



              {{-- <span style="display: inline-block;width: 60px;text-align: center;



              height: 60px;"> --}}



              <span style="display: inline-block;text-align: center;">



                @if(file_exists('pdfImage/product_logo/'.$productname.'.png'))



                <img src="{{URL::asset('pdfImage/product_logo/'.$productname.'.png') }}" style="width: 30px;">



                @else



                {{$productname}}



                @endif



              </span>



              @endif



              @endforeach



            </td>



          </tr>



        </table>



      </td>



    </tr>



  </table>







  <div class="footer">



    <div style="width:33%;display:inline-block;">



      <img src="{{URL::asset('pdfImage/logo-qdegrees.png')}}" style="height: 38px;">



    </div>



    <div style="width:33%;display:inline-block;text-align:center;">



      <div>



        <img src="{{URL::asset('pdfImage/footer-middle.jpg')}}" style="width: 100px;">



      </div>



    </div>



  </div>



</div>







<div class="page-break"></div>



<div class="pageA4" style="position: relative;padding-bottom:45px;margin-bottom: 28px; background:#fff; margin-bottom: 50px; padding:50px;">



  <table  width="100%">



    <tr>



      <td align='left'><h2 style="margin:0px;text-align:left;"> {{$value->name ?? ''}} - Branch - Product Score Card



      </h2>



    </td>



    <td align="right"><img src="{{URL::asset('pdfImage/logo.jpg')}}" style=" vertical-align:bottom; width:150px;    margin-left: auto;  display: block;" /></td>



  </tr>



</table>



<hr />



<table class="exceptional" width="100%" style="margin-bottom: 100px; text-align: left;">



  <thead>



    <tr>



      <th>S.No.</th>



      <th>Name of Product </th>



      <th>Scored  </th>



      <th>Scoreable</th>



      <th>Scored (%)-WaveIV </th>



      <th>Scored (%) – Wave - III</th>



      <th>Collection Manager  </th>







      <th>Reporting Manager  </th>



      <th>Role</th>



    </tr>



  </thead>



  <tbody>



    @php $i=1;$total=1;@endphp



    @isset($branchProduct[$value->id])



    @foreach ($branchProduct[$value->id] as $key=>$item)



    @if(count($item)>1)



    @php



    $totalScoredProduct=array_sum(array_column($item, 'scored'));



    $totalScroableProduct=array_sum(array_column($item, 'scoreable'));



    @endphp



    <tr>



      <td class="red">{{$i++}}</td>



      <td class="red"> {{$product_name[$key] ?? ''}} Overall</td>



      <td class="red"> {{ $totalScoredProduct }} </td>



      <td class="red">{{ $totalScroableProduct }} </td>



      @if($totalScroableProduct!=0)



      <td class="red">{{round(($totalScoredProduct/$totalScroableProduct)*100,2)}}%</td>



      @else



      <td class="red">0%</td>



      @endif







      <td class="red">NA</td>



      <td> </td>



      <td> </td>



      <td></td>



    </tr>



    @endif



    <tr>



      @foreach ($item as $k=>$v)



      <tr>



        <td>{{(count($item)==1)?$i++:''}}</td>



        <td>{{$product_name[$key] ?? ''}}</td>



        {{-- <td>{{$pro->audit_results->sum('score')}}</td> --}}



        <td>{{$v['scored']}}</td>



        <td>{{$v['scoreable']}}</td>



        @if($v['scoreable']!=0)



        <td>{{round(($v['scored']/$v['scoreable'])*100,2)}}%</td>



        @else



        <td>0%</td>



        @endif



        <td>NA</td>



        {{-- <td> {{$v['collection_manager']."($k)" ?? ''}} </td> --}}



        <td> {{$v['collection_manager'] ?? ''}} </td>



        {{-- <td>{{$value->location ?? ''}} </td> --}}



        <td>{{$v['reporting_manager'] ?? ''}} </td>



        <td>{{$v['role'] ?? ''}}</td>



      </tr>



      @endforeach



      @endforeach



      @endisset



    </tbody>



  </table>



  <div class="footer">



    <div style="width:33%;display:inline-block;"><img src="{{URL::asset('pdfImage/logo-qdegrees.png')}}" style="height: 38px;"></div>



    <div style="width:33%;display:inline-block;text-align:center;">



      <div><img src="{{URL::asset('pdfImage/footer-middle.jpg')}}" style="width: 100px;"></div>



    </div>



    <div style="width:33%;display:inline-block;text-align:right;">



      {{-- <div><img src="{{URL::asset('pdfImage/footer-right.jpg')}}"></div> --}}



    </div>



    



  </div>



</div>







<div class="page-break"></div>



@isset($collectionmanager[$value->id])



@foreach ($collectionmanager[$value->id] as $item)



<div class="pageA4 mastertab" style="position: relative;padding-bottom:45px;margin-bottom: 28px; background:#fff; margin-bottom: 50px; padding:50px;">



  <table  width="100%">



    <tr>



      <td align='left'><h2 style="margin:0px;text-align:left;">{{$value->name ?? ''}} - Collection Manager Score Card



      </h2>



    </td>



    <td align="right"><img src="{{URL::asset('pdfImage/logo.jpg')}}" style=" vertical-align:bottom; width:150px;    margin-left: auto;  display: block;" /></td>



  </tr>



</table>



<hr/>

</table>



@php



$finalTotal=[];



@endphp





<table class="exceptional tab2"  width="100%" style=" margin: 30px 0px;">



  <thead>



    <tr>



      <th>S.No.</th>



      <th>Type </th>



      <th>Agency Name</th>



      <th>Scored </th>



      <th>Scorable  </th>



      <th>Scored (%)</th>







    </tr>



  </thead>



  <tbody>



    @php 



    $i=1;



    $finalScore=$finalScoreable=0;

    $mergeScored=$mergeScoreable=0;

    $finalstring_data=[];

    @endphp

    @isset($item['product'])

    @foreach ($item['product'] as $ke=>$val)

    @foreach($val as $agencysingle) 



    <?php



    $type='';



    switch($agencysingle['type'] ?? ''){



      case 'agency':



      $type='Collection Agency';



      break;



      case 'branch':



      $type='Branch';



      break;



      case 'branchRepo':



      $type='BRANCH Repo';



      break;



      case 'agencyrepo':



      $type='Repo Agency';



      break;



      case 'yard':



      $type='Yard Agency';



      break;



    }

    $scoreddata = 0;

    $scoreddata = $agencysingle['scored'];

    if(strtolower($type) == 'branch'){

      if(strtolower($product_name[$ke]) == 'auto/used car loan' || strtolower($product_name[$ke]) == 'two wheeler loan' )

      {

        $finalstring_data['A']['value'] = 'Y';

        $finalstring_data['A']['product'] = $type;

        $finalstring_data['A']['scored'] = isset($finalstring_data['A']['scored']) ?  $finalstring_data['A']['scored'] + $agencysingle['scored'] : $agencysingle['scored'];

        $finalstring_data['A']['scoreable'] = isset($finalstring_data['A']['scoreable']) ?  $finalstring_data['A']['scoreable'] + $agencysingle['scoreable'] : $agencysingle['scoreable']; 



       }

        //elseif(strtolower($product_name[$ke]) == 'vintage recovery')

      // {

      //   $finalstring_data['B']['value'] = 'Y'; 

      //   $finalstring_data['B']['product'] = $type;

      //   $finalstring_data['B']['scored'] = isset($finalstring_data['B']['scored']) ?  $finalstring_data['B']['scored'] + $agencysingle['scored'] : $agencysingle['scored'];

      //   $finalstring_data['B']['scoreable'] = isset($finalstring_data['B']['scoreable']) ?  $finalstring_data['B']['scoreable'] + $agencysingle['scoreable'] : $agencysingle['scoreable']; 

      // }elseif(strtolower($product_name[$ke]) == 'fresh recovery')

      // {

      //   $finalstring_data['A']['value'] = 'Y';

      //   $finalstring_data['A']['product'] = $type;

      //   $finalstring_data['A']['scored'] = isset($finalstring_data['A']['scored']) ?  $finalstring_data['A']['scored'] + $agencysingle['scored'] : $agencysingle['scored'];

      //   $finalstring_data['A']['scoreable'] = isset($finalstring_data['A']['scoreable']) ?  $finalstring_data['A']['scoreable'] + $agencysingle['scoreable'] :  $agencysingle['scoreable'];   

      // }

      else

      {

       $finalstring_data['B']['value'] = 'Y'; 

       $finalstring_data['B']['product'] = $type;

       $finalstring_data['B']['scored'] = isset($finalstring_data['B']['scored']) ?  ($finalstring_data['B']['scored'] + $agencysingle['scored']) : $agencysingle['scored'];

       $finalstring_data['B']['scoreable'] = isset($finalstring_data['B']['scoreable']) ?  ($finalstring_data['B']['scoreable'] + $agencysingle['scoreable']) :  $agencysingle['scoreable'];

     }

   }



   if(strtolower($type) == 'collection agency'){

    if(strtolower($product_name[$ke]) == 'auto/used car loan' || strtolower($product_name[$ke]) == 'two wheeler loan' )

    {

      $finalstring_data['C']['value'] = 'Y';

      $finalstring_data['C']['product'] = $type;

      $finalstring_data['C']['scored'] = isset($finalstring_data['C']['scored']) ?  $finalstring_data['C']['scored'] + $agencysingle['scored'] : $agencysingle['scored'];

      $finalstring_data['C']['scoreable'] = isset($finalstring_data['C']['scoreable']) ?  $finalstring_data['C']['scoreable'] + $agencysingle['scoreable'] : $agencysingle['scoreable']; 



    }

    // elseif(strtolower($product_name[$ke]) == 'vintage recovery')

    // {

    //   $finalstring_data['F']['value'] = 'Y'; 

    //   $finalstring_data['F']['product'] = $type;

    //   $finalstring_data['F']['scored'] = isset($finalstring_data['F']['scored']) ?  $finalstring_data['F']['scored'] + $agencysingle['scored'] : $agencysingle['scored'];

    //   $finalstring_data['F']['scoreable'] = isset($finalstring_data['F']['scoreable']) ?  $finalstring_data['F']['scoreable'] + $agencysingle['scoreable'] : $agencysingle['scoreable']; 

    // }elseif(strtolower($product_name[$ke]) == 'fresh recovery')

    // {

    //   $finalstring_data['E']['value'] = 'Y';

    //   $finalstring_data['E']['product'] =$type;

    //   $finalstring_data['E']['scored'] = isset($finalstring_data['E']['scored']) ?  $finalstring_data['E']['scored'] + $agencysingle['scored'] : $agencysingle['scored'];

    //   $finalstring_data['E']['scoreable'] = isset($finalstring_data['E']['scoreable']) ?  $finalstring_data['E']['scoreable'] + $agencysingle['scoreable'] :  $agencysingle['scoreable'];   

    // }

    else

    {

     $finalstring_data['D']['value'] = 'Y'; 

     $finalstring_data['D']['product'] = $type;

     $finalstring_data['D']['scored'] = isset($finalstring_data['D']['scored']) ?  ($finalstring_data['D']['scored'] + $agencysingle['scored']) : $agencysingle['scored'];

     $finalstring_data['D']['scoreable'] = isset($finalstring_data['D']['scoreable']) ?  ($finalstring_data['D']['scoreable'] + $agencysingle['scoreable']) :  $agencysingle['scoreable'];

   }

 }

 if(strtolower($type) == 'yard agency'){

  $finalstring_data['E']['value'] = 'Y'; 

  $finalstring_data['E']['product'] = $type;

  $finalstring_data['E']['scored'] = isset($finalstring_data['E']['scored']) ?  $finalstring_data['E']['scored'] + $agencysingle['scored'] : $agencysingle['scored'];

  $finalstring_data['E']['scoreable'] = isset($finalstring_data['E']['scoreable']) ?  $finalstring_data['E']['scoreable'] + $agencysingle['scoreable'] : $agencysingle['scoreable'];

}

if(strtolower($type) == 'repo agency'){

 $finalstring_data['F']['value'] = 'Y';

 $finalstring_data['F']['product'] = $type;

 $finalstring_data['F']['scored'] = isset($finalstring_data['F']['scored']) ?  $finalstring_data['F']['scored'] + $agencysingle['scored'] : $agencysingle['scored'];

 $finalstring_data['F']['scoreable'] = isset($finalstring_data['F']['scoreable']) ?  $finalstring_data['F']['scoreable'] + $agencysingle['scoreable'] : $agencysingle['scoreable'];

}

if(strtolower($type) == 'branch repo'){

 $finalstring_data['G']['value'] = 'Y';

 $finalstring_data['G']['product'] = $type;

 $finalstring_data['G']['scored'] = isset($finalstring_data['G']['scored']) ?  $finalstring_data['G']['scored'] + $agencysingle['scored'] : $agencysingle['scored'];

 $finalstring_data['G']['scoreable'] = isset($finalstring_data['G']['scoreable']) ?  $finalstring_data['G']['scoreable'] + $agencysingle['scoreable'] : $agencysingle['scoreable']; 

}     

// if(isset($finalTotal[$type.'-'.($product_name[$ke]??'')])){



//   $finalTotal[$type.'-'.($product_name[$ke]??'')]['scored']=$finalTotal[$type.'-'.($product_name[$ke]??'')]['scored']+$agencysingle['scored'];



//   $finalTotal[$type.'-'.($product_name[$ke]??'')]['scoreable']=$finalTotal[$type.'-'.($product_name[$ke]??'')]['scoreable']+$agencysingle['scoreable'];



// }



// else{



//   $finalTotal[$type.'-'.($product_name[$ke]??'')]=[



//     'scored'=>$agencysingle['scored'],



//     'scoreable'=>$agencysingle['scoreable'],



//   ];



// }



//$finalScore=$finalScore+$agencysingle['scored'];



//$finalScoreable=$finalScoreable+$agencysingle['scoreable'];







?>



<tr>



  <td>{{$i++}}</td>



  <td> {{$type}}- {{$product_name[$ke] ?? ''}}</td>



  <td>{{$agencysingle['agency'] ?? ''}} </td>



  <td>{{$agencysingle['scored']}}</td>



  <td>{{$agencysingle['scoreable']}}</td>



  @if($agencysingle['scoreable']!=0)



  <td style="background:#f8cbaa;">{{round(($agencysingle['scored']/$agencysingle['scoreable'])*100,2)}}%</td>



  @else



  <td style="background:#f8cbaa;">0%</td>



  @endif



</tr>



@endforeach



@endforeach



@endif

          <?php

          $getArray=array('A','B','C','D','E','F','G');

          $concat='';$calculation_data=[];



          foreach($getArray as $values)

          {

            $checkY = '';

            $checkN = '';

            foreach($finalstring_data as  $keys=>$merge_string)

            {



              $calculation_data[$keys]=array('scored'=>$merge_string['scored'],'scoreable'=>$merge_string['scoreable'],'product_nm'=>$merge_string['product']);

              if($keys == $values){

                $checkY = 'Y';

              }else{

                $checkN ='N';

              }

            

            }

            $concat .= !empty($checkY)?'Y':'N';



          }

          //echo $concat;

          $get_stringData=DB::table('6040_calculation')->select('c60','c40','c100','pk')->where('uk',$concat)->first();

          $finalTotalValues=$finalTotal60Values=$finalTotal40Values=$finalTotal100Values=$getC60Data=$getC100Data=$getC40Data=[];

          if(!empty($get_stringData)){

            foreach($calculation_data as $data_key=>$cal_values)

                {

                  //echo $cal_values['scoreable'];

                  if(!empty($get_stringData->c60))

                  { //echo $data_key;

                    $explode_values=explode(',',$get_stringData->c60);

                    if(count($explode_values) > 1)

                    {

                      foreach($explode_values as $explodeloop_values)

                      {

                        if($explodeloop_values == $data_key){

                          $finalTotalValues['scored']=  isset($finalTotalValues['scored']) ? $finalTotalValues['scored'] + (($cal_values['scored'] * 60)/100) : (($cal_values['scored'] * 60)/100);

                          $finalTotalValues['scoreable']= isset($finalTotalValues['scoreable']) ? $finalTotalValues['scoreable'] +($cal_values['scoreable'] * 60)/100  : (($cal_values['scoreable'] * 60)/100) ;

                          $finalTotalValues['score%']= !empty($finalTotalValues['scoreable'] && $finalTotalValues['scored']) ? round(($finalTotalValues['scored']/$finalTotalValues['scoreable'])*100,2) : '0';



                          $finalTotal60Values['scored']= isset($finalTotal60Values['scored']) ? $finalTotal60Values['scored'] + (($cal_values['scored'] * 60)/100) : (($cal_values['scored'] * 60)/100);

                          $finalTotal60Values['scoreable']= isset($finalTotal60Values['scoreable']) ? $finalTotal60Values['scoreable'] + (($cal_values['scoreable'] * 60)/100) : (($cal_values['scoreable'] * 60)/100);



                          $finalTotal60Values['score%']= !empty($finalTotal60Values['scoreable'] && $finalTotal60Values['scored']) ? round(($finalTotal60Values['scored']/$finalTotal60Values['scoreable'])*100,2) : '0';

                          $finalTotal60Values['product']=$cal_values['product_nm'];

                        } 

                      }

                    }else

                    {

                     if($get_stringData->c60 == $data_key){

                       $finalTotalValues['scored']=  isset($finalTotalValues['scored']) ? $finalTotalValues['scored'] + (($cal_values['scored'] * 60)/100) : (($cal_values['scored'] * 60)/100);

                       $finalTotalValues['scoreable']= isset($finalTotalValues['scoreable']) ? $finalTotalValues['scoreable'] +($cal_values['scoreable'] * 60)/100  : (($cal_values['scoreable'] * 60)/100) ;





                       $finalTotal60Values['scored']= isset($finalTotal60Values['scored']) ? $finalTotal60Values['scored'] + (($cal_values['scored'] * 60)/100) : (($cal_values['scored'] * 60)/100);

                       $finalTotal60Values['scoreable']= isset($finalTotal60Values['scoreable']) ? $finalTotal60Values['scoreable'] + (($cal_values['scoreable'] * 60)/100) : (($cal_values['scoreable'] * 60)/100);



                       $finalTotal60Values['score%']= !empty($finalTotal60Values['scoreable'] && $finalTotal60Values['scored']) ? round(($finalTotal60Values['scored']/$finalTotal60Values['scoreable'])*100,2) : '0';

                       $finalTotal60Values['product']=$cal_values['product_nm'];

                     }

                   }

                   $getC60Data['scored']=isset($finalTotal60Values['scored']) ? $finalTotal60Values['scored'] : '';

                   $getC60Data['scoreable']=isset($finalTotal60Values['scoreable']) ? $finalTotal60Values['scoreable'] : '' ;

                   $getC60Data['score%']=isset($finalTotal60Values['score%']) ? $finalTotal60Values['score%'] : '';

                   $getC60Data['type']=isset($finalTotal60Values['product']) ? $finalTotal60Values['product'] : '';

                 }

                 



                 if(!empty($get_stringData->c100))

                 {

                  $explode_values=explode(',',$get_stringData->c100);

                  if(count($explode_values) > 1)

                  {

                    foreach($explode_values as $explodeloop_values)

                    {

                      if($explodeloop_values == $data_key){

                        $finalTotalValues['scored']=  isset($finalTotalValues['scored']) ? $finalTotalValues['scored'] + ($cal_values['scored']) : $cal_values['scored'];

                        $finalTotalValues['scoreable']= isset($finalTotalValues['scoreable']) ? $finalTotalValues['scoreable'] +($cal_values['scoreable']) : $cal_values['scoreable'] ;



                        $finalTotal100Values['scored']= isset($finalTotal100Values['scored']) ? $finalTotal100Values['scored'] + ($cal_values['scored']) : $cal_values['scored'];

                        $finalTotal100Values['scoreable']= isset($finalTotal100Values['scoreable']) ? $finalTotal100Values['scoreable'] + ($cal_values['scoreable']) : $cal_values['scoreable'];

                        $finalTotal100Values['score%']= !empty($finalTotal100Values['scoreable'] && $finalTotal100Values['scored']) ? round(($finalTotal100Values['scored']/$finalTotal100Values['scoreable'])*100,2) : '0';

                        $finalTotal00Values['product']=$cal_values['product_nm'];

                      } 

                    }

                  }else

                  {

                    if($get_stringData->c100 == $data_key){

                     $finalTotalValues['scored']=  isset($finalTotalValues['scored']) ? $finalTotalValues['scored'] + ($cal_values['scored']) : $cal_values['scored'];

                     $finalTotalValues['scoreable']= isset($finalTotalValues['scoreable']) ? $finalTotalValues['scoreable'] +($cal_values['scoreable'])  : $cal_values['scoreable'] ;

                     $finalTotal100Values['scored']= isset($finalTotal100Values['scored']) ? $finalTotal100Values['scored'] + ($cal_values['scored']) : $cal_values['scored'];

                     $finalTotal100Values['scoreable']= isset($finalTotal100Values['scoreable']) ? $finalTotal100Values['scoreable'] + ($cal_values['scoreable']) : $cal_values['scoreable'];

                     $finalTotal100Values['score%']= !empty($finalTotal100Values['scoreable'] && $finalTotal100Values['scored']) ? round(($finalTotal100Values['scored']/$finalTotal100Values['scoreable'])*100,2) : '0';

                     $finalTotal00Values['product']=$cal_values['product_nm'];

                   }

                 }

                 $getC100Data['scored']=isset($finalTotal100Values['scored']) ? $finalTotal100Values['scored'] : '';

                 $getC100Data['scoreable']=isset($finalTotal100Values['scoreable']) ? $finalTotal100Values['scoreable'] : '' ;

                 $getC100Data['score%']=isset($finalTotal100Values['score%']) ? $finalTotal100Values['score%'] : '';

                 $getC100Data['type']=isset($finalTotal00Values['product']) ? $finalTotal00Values['product'] : '';

               }

               if(!empty($get_stringData->c40))

               {

                $explode_values=explode(',',$get_stringData->c40);

                if(count($explode_values) > 1)

                {

                  foreach($explode_values as $explodeloop_values)

                  {

                    if($explodeloop_values == $data_key){

                      $finalTotalValues['scored']=  isset($finalTotalValues['scored']) ? $finalTotalValues['scored'] + (($cal_values['scored'] * 40)/100) : (($cal_values['scored'] * 40)/100);

                      $finalTotalValues['scoreable']= isset($finalTotalValues['scoreable']) ? $finalTotalValues['scoreable'] +($cal_values['scoreable'] * 40)/100  : (($cal_values['scoreable'] * 40)/100) ;



                      $finalTotal40Values['scored']= isset($finalTotal40Values['scored']) ? $finalTotal40Values['scored'] + (($cal_values['scored'] * 40)/100) : (($cal_values['scored'] * 40)/100);

                      $finalTotal40Values['scoreable']= isset($finalTotal40Values['scoreable']) ? $finalTotal40Values['scoreable'] + (($cal_values['scoreable'] * 40)/100) : (($cal_values['scoreable'] * 40)/100);



                      $finalTotal40Values['score%']= !empty($finalTotal40Values['scoreable'] && $finalTotal40Values['scored']) ? round(($finalTotal40Values['scored']/$finalTotal40Values['scoreable'])*100,2) : '0';

                      $finalTotal40Values['product']=$cal_values['product_nm'];

                    } 

                  }

                }else

                {

                  if($get_stringData->c40 == $data_key){

                   $finalTotalValues['scored']=  isset($finalTotalValues['scored']) ? $finalTotalValues['scored'] + (($cal_values['scored'] * 40)/100) : (($cal_values['scored'] * 40)/100);

                   $finalTotalValues['scoreable']= isset($finalTotalValues['scoreable']) ? $finalTotalValues['scoreable'] +($cal_values['scoreable'] * 40)/100  : (($cal_values['scoreable'] * 40)/100) ;

                   $finalTotalValues['score%']= !empty($finalTotalValues['scoreable'] && $finalTotalValues['scored']) ? round(($finalTotalValues['scored']/$finalTotalValues['scoreable'])*100,2) : '0';

                   $finalTotal40Values['product']=$cal_values['product_nm'];

                 }

               }

               $getC40Data['scored']=isset($finalTotal40Values['scored']) ? $finalTotal40Values['scored'] : '';

               $getC40Data['scoreable']=isset($finalTotal40Values['scoreable']) ? $finalTotal40Values['scoreable'] : '' ;

               $getC40Data['score%']=isset($finalTotal40Values['score%']) ? $finalTotal40Values['score%'] : '';

               $getC40Data['type']=isset($finalTotal40Values['product']) ? $finalTotal40Values['product'] : '';

             }

           }



          }

          if(!empty($finalTotalValues) && $finalTotalValues['scoreable'] != 0)

          {

            $finalTotalValues['score%']=round(($finalTotalValues['scored']/$finalTotalValues['scoreable'])*100,2);

          } else {

            $finalTotalValues['score%']=0;

          }



          ?>

          <?php

          if((!empty($getC40Data) && !empty($getC60Data)) || (!empty($getC40Data) && !empty($getC60Data) && !empty($getC100Data)))

           { $C40=$C60=$C100=[];

            $C40=(!empty($getC40Data)) ? $getC40Data : $C40;

            $C60=(!empty($getC60Data)) ? $getC60Data : $C60;

            $C100=(!empty($getC100Data)) ? $getC100Data : $C100; 

            $getValues=array('60'=>$C60, '40'=>$C40,'100'=>$C100);

            ?>

            <tr><td class="red">Final Score Calculation </td></tr></tbody>

            <table class="exceptional tab3" width="100%">



              <thead>



                <tr>



                  <th>Agency Type</th>



                  <th> Scored </th>



                  <th>Scorable </th>



                  <th>Scored (%)

                  </th>

                </tr>



              </thead>

              <tbody>

                <?php

                  if(!empty($getValues))

               {

                foreach($getValues as $keyss=> $printData)

                { if(isset($printData['scored']) && $printData['scored'] !=0){?><tr>

                  <td>{{$printData['type'] ?? ''}}Score ({{$keyss}}%)</td>

                  <td>{{$printData['scored'] ?? 0}}</td>

                  <td>{{$printData['scoreable'] ?? 0}}</td>

                  <td>{{$printData['score%'] ?? 0}}</td>

                </tr>

              <?php } }?>

               <tr>

                  <td class="red">Final Score </td>



                  <td class="red"> {{$finalTotalValues['scored'] ?? 0}} </td>



                  <td class="red"> {{$finalTotalValues['scoreable']?? 0}} </td>



                  @if(isset($finalTotalValues['scored']) && $finalTotalValues['scoreable']!=0)



                  <td class="red">{{round(($finalTotalValues['scored']/$finalTotalValues['scoreable'])*100,2)}}%</td>



                  @else



                  <td class="red">0%</td>



                  @endif



                </tr>

             <?php }

                ?>

              </tbody></table>



              <?php  }else{

                ?>

                <tr>



                  <td colspan="2"></td>



                  <td class="red">Final Score </td>



                  <td class="red"> {{$finalTotalValues['scored'] ?? 'Excluded'}} </td>



                  <td class="red"> {{$finalTotalValues['scoreable']?? 'Excluded'}} </td>



                  @if(isset($finalTotalValues['scored']) && $finalTotalValues['scoreable']!=0)



                  <td class="red">{{round(($finalTotalValues['scored']/$finalTotalValues['scoreable'])*100,2)}}%</td>



                  @else



                  <td class="red">Excluded</td>



                  @endif



                </tr>

              </tbody>

            <?php } ?>  



          </table>

          <table class="exceptional tab1" width="100%">



            <thead>



              <tr>



                <th>Products</th>



                <th> Collection Managers Name  </th>



                <th> Emp ID   </th>

                 <th> Base Location  </th>



                <th>Final Score in Wave-IV



                </th>



                <th>Scored (%) in Wave-III



                </th>



              </tr>



            </thead>



            <tbody>

              @php



              $productimage='';   



              if(isset($item['product'])){

              if(isset($product_name[$ke])){



              $productname='';



              $productname=str_replace('/','',($product_name[$ke]));      



              $productname=str_replace(' ','',($productname));  



              if(file_exists('pdfImage/product_logo/'.$productname.'.png')){



              $productimage=$productimage."<img src=".URL::asset('pdfImage/product_logo/'.$productname.'.png')." style='width: 30px;' alt=".$productname.">";



            }

            else{



            $productimage=$productimage.','.$productname;



          }

        }



        else{



        $productimage=$productimage."<img src=".URL::asset('pdfImage/product_logo/'.($product_name[$ke] ?? '').'.png')." style='width: 30px;' alt=".$productname.">";



      }

    }

    @endphp



    <tr>



      <td>



        {!! $productimage !!}



      </td>



      <td class="red"> {{$item['collection_manager'] ?? ''}}  </td>



      <td class="red">{{(isset($item['collection_manager_empid']) && $item['collection_manager_empid']!='')? $item['collection_manager_empid'] : 'NA'}}   </td>

       <td class="red"> {{$item['collection_manager_baselocation'] ?? ''}}  </td>

      <?php

      if(isset($finalTotalValues['scoreable'])){

        if($finalTotalValues['scoreable'] == 0){?>

          <td class="red">0%</td>

        <?php }else{?>

          <td class="red">{{round(($finalTotalValues['scored']/$finalTotalValues['scoreable'])*100,2)}}%</td>

          <?php

        }

      } 

      ?>

      <td class="red">NA



      </td>



    </tr>



  </tbody>



</table>

<div class="footer">



  <div style="width:33%;display:inline-block;"><img src="{{URL::asset('pdfImage/logo-qdegrees.png')}}" style="height: 38px;"></div>



  <div style="width:33%;display:inline-block;text-align:center;">



    <div><img src="{{URL::asset('pdfImage/footer-middle.jpg')}}" style="width: 100px;"> </div>



  </div>



  <div style="width:33%;display:inline-block;text-align:right;">



    <div><img src="{{URL::asset('pdfImage/footer-right.jpg')}}"></div>



  </div>



</div>



<div class="page-break"></div>



</div>



@endforeach



@endif

@endforeach



</body>



</html>

<script type="text/javascript">



  window.addEventListener('load', function() {

    let ParentsTabs = document.querySelectorAll('.mastertab');

    ParentsTabs.forEach(parent => {

      let tab1 = parent.querySelector('.tab1');

      let tab2 = parent.querySelector('.tab2');

      let tab3 = parent.querySelector('.tab3');

      tab1.after(tab2);

      if(tab3 !== null){

      tab2.after(tab3);}

    });

  });

</script>