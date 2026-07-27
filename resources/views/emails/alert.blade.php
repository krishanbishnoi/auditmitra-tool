<div>
   <div>Hi,</div><br/>
   <div>Greetings!</div><br/>
   <div>Please find attached Audit Alerts for {{$otherDetails['name']}} ({{$data->product->name ?? ''}}), details as mentioned below:</div>
   <br/>
   <div>
     <table style="border: 1px solid #dee2e6; text-align:center" >
        <thead style="background-color:rgb(192, 0, 0);">
            <tr>
                <th>S.No</th>
                <th>Region</th>
                <th>State</th>
                <th>City</th>
                <th>Collection Agency</th>
                <th>Product</th>
                <th>Collection Manager</th>
                <th>Number of Gaps Found</th>
                <th>GAP</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="padding: .75rem;">1</td>
                <td style="padding: .75rem;">{{$otherDetails['region']}}</td>
                <td style="padding: .75rem;">{{$otherDetails['state']}}</td>
                <td style="padding: .75rem;">{{$otherDetails['city']}}</td>
                <td style="padding: .75rem;">{{$otherDetails['name']}}</td>
                <td style="padding: .75rem;">{{$data->product->name ?? ''}}</td>
                <td style="padding: .75rem;">{{$otherDetails['collection'] ?? ''}}</td>
                <td style="padding: .75rem;">{{$data->redAlert->count() ?? 0}}</td>
                <td style="padding: .75rem;">
                    <ol>
                        @foreach($data->redAlert as $k=>$v)
                            <li>{{$v->subParameter->sub_parameter}}</li>
                        @endforeach
                    </ol>
                </td>
                <td style="padding: .75rem;">
                    <ol>
                        @foreach($data->redAlert as $k=>$v)
                            <li>{{$auditResult[$v->subParameter->id]}}</li>
                        @endforeach
                    </ol>
                </td>
            </tr>
        </tbody>
     </table>
   </div>
   <br/>
   <div>
    <a href="{{$url}}">Click here</a>
   </div>
</div>