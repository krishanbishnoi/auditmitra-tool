 <style>
    table td:first-child, 
    table th:first-child {
        white-space: normal !important;
        word-break: break-word;
        overflow-wrap: break-word;
        max-width: 300px; /* adjust to control wrapping width */
    }
    
 </style>

 <table id="crossTabTable" class="table table-striped table-bordered nowrap" style="width:100%">  
    @if($match_field == 1 && $match_case == 1)                         
    <thead class="table-light">
        <tr>
            <th style="text-align:center;">Row</th>
            <th style="text-align:center;">Count</th>
            <th style="text-align:center;">Score</th>
            <th style="text-align:center;">Score Percentage</th>
        </tr>
    </thead>
    <tbody>
        @foreach($result as $r)
        <tr>
            <td style="text-align:center;">{{$r->row_name}}</td>
            <td style="text-align:center;">{{round($r->audit_count)}}</td>  
            <td style="text-align:center;">{{round($r->audit_score)}}</td> 
            <td style="text-align:center;">{{round($r->score_percentage)}}%</td>         
        </tr>  
        @endforeach  
    </tbody>
    @elseif($match_case == 2 && $match_field == 1)
    <thead class="table-light">
        <tr>
            <th style="text-align:center;">Row</th>           
            <th style="text-align:center;">Count</th>                                 
        </tr>
    </thead>
    <tbody>
       @foreach($result as $r)
        <tr>
            <td style="text-align:center;">{{$r->row_name}}</td>            
            <?php $countkey="repeat_".$r->row_id; ?>  
            <td style="text-align:center;">{{ $r->$countkey }}</td>         
        </tr>  
        @endforeach    
    </tbody>
    @elseif($match_case == 2 && $match_field > 1)
    <thead class="table-light">
        <tr>
            <th rowspan="2" style="text-align:center;">Row</th>
            @foreach($lastSixCycles as $key=>$c)
            <th style="text-align:center;">{{$key}}</th>                    
            @endforeach                      
        </tr>
         <tr>            
            @foreach($lastSixCycles as $key=>$c)   
            <th style="text-align:center;">Count</th> 
            @endforeach                               
        </tr>
    </thead>
    <tbody>
        @foreach($result as $r)
        <tr>
            <td style="text-align:center;">{{$r->row_name}}</td>
            @foreach($lastSixCycles as $c)  
            <?php $countkey="repeat_".$c; ?>     
            <td style="text-align:center;">{{ $r->$countkey }}</td>
            @endforeach    
        </tr>  
        @endforeach  
    </tbody>
    @elseif($match_case == 3)

<style>
    /* Thicker border after each cycle group */
    .cycle-end {
        border-right: 2px solid #adb5bd !important;
    }

    /* Header background for cycles */
    .cycle-header {
        background-color: #f1f3f5;
        font-weight: 600;
    }

    /* Sub-header styling */
    .cycle-sub {
        background-color: #f8f9fa;
        font-size: 12px;
    }

    /* Center all cells */
    #crossTabTable th,
    #crossTabTable td {
        text-align: center;
        vertical-align: middle;
    }
</style>

<thead class="table-light">
<tr>
    <th rowspan="2">Row</th>
    @foreach($lastSixCycles as $key => $c)
        <th colspan="3" class="cycle-header">
            {{ $key }}
        </th>
    @endforeach
</tr>
<tr>
    @foreach($lastSixCycles as $key => $c)
        <th class="cycle-sub">Open</th>
        <th class="cycle-sub">Closed</th>
        <th class="cycle-sub cycle-end">Total</th>
    @endforeach
</tr>
</thead>

<tbody>
@foreach($result as $r)
<tr>
    <td>{{ $r->row_name }}</td>

    @foreach($lastSixCycles as $c)
        @php
            $openKey   = 'open_'.$c;
            $closedKey = 'closed_'.$c;
            $totalKey  = 'total_'.$c;
        @endphp

        <td>{{ $r->$openKey ?? 0 }}</td>
        <td>{{ $r->$closedKey ?? 0 }}</td>
        <td class="cycle-end">{{ $r->$totalKey ?? 0 }}</td>
    @endforeach
</tr>
@endforeach
</tbody>
{{-- @endif --}}


    @else
    <thead class="table-light">
        <tr>
            <th rowspan="2" style="text-align:center;">Row</th>
            @foreach($lastSixCycles as $key=>$c)
            <th colspan="2" style="text-align:center;">{{$key}}</th>                    
            @endforeach                      
        </tr>
         <tr>            
            @foreach($lastSixCycles as $key=>$c)           
            <th style="text-align:center;">Score</th> 
            <th style="text-align:center;">Count</th> 
            @endforeach                               
        </tr>
    </thead>
    <tbody>
        @foreach($result as $r)
        <tr>
            <td style="text-align:center;">{{$r->row_name}}</td>
            @foreach($lastSixCycles as $c)  
            <?php $scorekey="score_".$c; $countkey="count_".$c; $final_score=($r->$scorekey != "") ? $r->$scorekey : 0; ?>         
            <td style="text-align:center;">{{ $final_score."%" }}</td>
            <td style="text-align:center;">{{ $r->$countkey }}</td>
            @endforeach    
        </tr>  
        @endforeach  
    </tbody>
    @endif
 </table>

 <script>
     $('#crossTabTable').DataTable({
        responsive: true,
        paging: true,
        searching: true,
        ordering: true,
        pageLength: 5,
        lengthMenu: [5, 10, 25],
    });
 </script>