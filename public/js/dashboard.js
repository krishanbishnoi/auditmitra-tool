var url=jQuery('#url').val();
function indiaMap(data){
    // var ravindra=Highcharts.maps["countries/in/in-all"].features;
    // jQuery.each(ravindra, function (key, value) {
    //     console.log(value.properties['hc-key'],value.properties['name']);
    // })


    // Create the chart
    Highcharts.mapChart('map', {
        chart: {
            map: 'countries/in/custom/in-all-disputed'
        },
        tooltip: {
            formatter: function () {
                // console.log(this)
                return this.key+':'+this.point.value+'%';
            }
        },
        title: {
            text: 'National Score',
            align:'left',
            // x: 70
        },

        subtitle: {
            text: ''
        },

        mapNavigation: {
            enabled: true,
            buttonOptions: {
                verticalAlign: 'bottom'
            }
        },

        colorAxis: {
            min: 0
        },

        series: [{
            data: data,
            name: 'Score',
            states: {
                hover: {
                    color: '#BADA55'
                }
            },
            dataLabels: {
                enabled: false,
                format: '{point.name}'
            },
        }],
        plotOptions: {
            series: {
                 cursor: 'pointer',
                 point: {
                     events: {
                        click: function() {
                            // console.log(this)
                            showStateBranch(this.name,this.value)
                        }
                    }
                }
            }
        }
    });
    jQuery('.highcharts-credits').hide();
}

function showProduct(){
    var lob=jQuery('#productlob').val();
    var audit_cycle=jQuery('#audit_cycle').val();
    var token=jQuery('#token').val();
    var data={'productlob':lob,'audit_cycle':audit_cycle,'_token':token};
    jQuery.ajax({
        type: "post",
        url: url+"/all-porudct" ,
        data:data,
        success: function (res) {
            console.log(res)
            if (res) {
                var html='';
                var total=0;
                jQuery.each(res.data, function (key, value) {
                    // total=total+value.point
                    total=total+1;
                   html=html+ `<div class="row productcontent"><div class="col-md-6">${value.lob} : ${value.name}</div>
                                    <div class="col-md-6">Score : ${((value.point/value.total)*100).toFixed(2)}%</div></div>`
                });
                jQuery('.productBack').html(html)
                jQuery('#totalPoint').html(total)
                jQuery('#show-product').modal('show')
            }
        }

    });

}

    jQuery('#nationalZone').on('change',function(){
        var cid = jQuery(this).val();
        getState(cid,'#nationalState')

    })
    jQuery('#zone').on('change',function(){
        var cid = jQuery(this).val();
        getState(cid,'#State')

    })
    function getState(cid,selector){
        jQuery.ajax({
            type: "get",
            url: url+"/getStates/" + cid,
            success: function (res) {
                if (res) {
                    jQuery(selector).empty();
                     jQuery(selector).append('<option value="all">All</option>');
                    jQuery.each(res, function (key, value) {
                        jQuery(selector).append('<option value="' + key + '">' + value + '</option>');
                    });
                }
            }

        });
    }
    jQuery('#nationalState').on('change',function(){
        var cid = jQuery(this).val();
        getBranch(cid,"#nationalBranch")
    })
    jQuery('#State').on('change',function(){
        var cid = jQuery(this).val();
        getBranch(cid,"#branch")
    })

    function getBranch(cid,selector){
        jQuery.ajax({
            type: "get",
            url: url+"/get-branch/" + cid,
            success: function (res) {
                if (res) {
                    jQuery(selector).empty();
                     jQuery(selector).append('<option value="all">All</option>');
                    jQuery.each(res.data, function (key, value) {
                        jQuery(selector).append('<option value="' + value.id + '">' + value.name + '</option>');
                    });
                }
            }

        });
    }
jQuery('#nationalResult').on('click',function(){
    fetchNationalScore();
})
function fetchNationalScore(){
    var product=jQuery('#nationalProduct').val()
    var lob=jQuery('#nationalLob').val()
    var zone=jQuery('#nationalZone').val()
    var audit_cycle=jQuery('#nationalAudit_cycle').val()
    var audit_cycle_custom=''
    if(audit_cycle=='custom'){
        audit_cycle_custom=jQuery('#nationalAudit_cycle_custom').val()
    }
    var state=jQuery('#nationalState').val()
    var branch=jQuery('#nationalBranch').val()
    var token=jQuery('#token').val();
    var data={
        'product':product,
        'lob':lob,
        'zone':zone,
        'audit_cycle':audit_cycle,
        'audit_cycle_custom':audit_cycle_custom,
        'state':state,
        'branch':branch,
        '_token':token
    };
    jQuery.ajax({
        type: "post",
        url: url+"/fetch-map",
        data:data,
        success: function (res) {
            if (res) {
               jQuery('#NationalTotal').html(res.total+'%')
                indiaMap(res.data);
            }
        }

    });
}
function showStateBranch(name,value){
    jQuery.ajax({
        type: "get",
        url: url+"/get-state-data/"+name,
        success: function (res) {
            if (res) {
                var html=''
                var total=0
                jQuery.each(res.data, function (key, value) {
                    html=html+`<tr>
                    <td>${key}</td>
                    <td>0%</td>
                    <td>${value}%</td>
                    </tr>`;
                    total=total+value;
                });
                jQuery('#showStateBranchBody').html(html)
                jQuery('#stateTotal').html(value)
                jQuery('#stateName').html(name)
                jQuery('#showStateBranch').modal('show')
            }
        }

    });
}
jQuery('.filterShow').on('click',function(){
    var id=jQuery(this).data('id')
    var name=jQuery(this).data('name')
    jQuery('.tableParameter').hide()
    getAgency(id,name)

})
jQuery('#agencyData').on('click','.agencyParameter',function(){
    var id=jQuery(this).data('id')
    jQuery.ajax({
        type: "get",
        url: url+"/get-agencies-parameter/"+id,
        success: function (res) {
            if (res.data!=null) {
                var html=''
                var total=0
                var data=[]
                var name=[]
                jQuery.each(res.data.audit_parameter_result, function (key, value) {
                    data.push({'value':value.with_fatal_score,
                        'name':value.parameter_detail.parameter
                })
                    name.push(value.parameter_detail.parameter)
                    // <td>${value.orignal_weight}</td>
                    html=html+ `<tr>
                    <td><a class="parameterDetail" data-id="${value.id}">${value.parameter_detail.parameter}</a></td>
                    <td>${value.with_fatal_score}</td>
                    <td>
                            ${(value.temp_weight>value.with_fatal_score)?
                            '<i class="fa fa-arrow-down text-danger"></i>'
                            :
                            '<i class="fa fa-arrow-up text-success"></i>'
                }
                        </td>
                    </tr>`
                })
                data.sort(function(a, b){
                    return a.value-b.value
                });
                pareto(data,name)
                jQuery('#parameterdata').html(html)
                jQuery('.tableParameter').show()
            }
            else{
                jQuery('#parameterdata').html(`<tr><td colspan="3">NA</td></tr>`)
                jQuery('.tableParameter').show()
            }
        }

    });
})
function getAgency(id,name){

    jQuery.ajax({
        type: "get",
        url: url+"/get-agencies/"+id,
        success: function (res) {
            if (res) {
                var html=''
                var total=0
                jQuery.each(res.data, function (key, value) {
                    if(res.point.hasOwnProperty(value.id)){
                    html=html+ `<tr class="agencyParameter cursor" data-id="${value.id}">
                        <td><a>${value.name}</a></td>
                        <td>${(res.point.hasOwnProperty(value.id))?res.point[value.id]:'NA'}</td>
                    </tr>`
                    }
                })
                // jQuery.each(res.branch, function (key, value) {
                //     if(res.branchpoint.hasOwnProperty(value.id)){
                //     html=html+ `<tr class="agencyParameter cursor" data-id="${value.id}">
                //         <td><a>${value.name}</a></td>
                //         <td>${(res.branchpoint.hasOwnProperty(value.id))?res.branchpoint[value.id]:'NA'}</td>
                //     </tr>`
                //     }
                // })
                // jQuery.each(res.data, function (key, value) {
                //     if(res.point.hasOwnProperty(value.id)){
                //     html=html+ `<tr class="agencyParameter cursor" data-id="${value.id}">
                //         <td><a>${value.name}</a></td>
                //         <td>${(res.point.hasOwnProperty(value.id))?res.point[value.id]:'NA'}</td>
                //     </tr>`
                //     }
                // })
                jQuery('#agencyData').html(html)
                jQuery('#title').html(`Agency wise Score of ${name}`)
                jQuery('#pareto').hide()
                jQuery('#add-category').modal('show')
            }
        }

    });
}
jQuery('#lob_audit_cycle').on('change',function(e){
    console.log(e.target.value)
    if(e.target.value=='custom'){
        jQuery('input[name="lob_audit_cycle_custom"]').show();
        jQuery('input[name="lob_audit_cycle_custom"]').daterangepicker();
    }
})
jQuery('#product_audit_cycle').on('change',function(e){
    console.log(e.target.value)
    if(e.target.value=='custom'){
        jQuery('input[name="product_audit_cycle_custom"]').show();
        jQuery('input[name="product_audit_cycle_custom"]').daterangepicker();
    }
})
jQuery('#nationalAudit_cycle').on('change',function(e){
    console.log(e.target.value)
    if(e.target.value=='custom'){
        jQuery('input[name="nationalAudit_cycle_custom"]').show();
        jQuery('input[name="nationalAudit_cycle_custom"]').daterangepicker();
    }
})
jQuery('#filteraudit_cycle').on('change',function(e){
    console.log(e.target.value)
    if(e.target.value=='custom'){
        jQuery('input[name="filteraudit_cycle_custom"]').show();
        jQuery('input[name="filteraudit_cycle_custom"]').daterangepicker();
    }
})

function pareto(data,name){
    var value=[];
    var label=[];
    data.map((item)=>{
        label.push(item.name);
        value.push(item.value);
    })
    // console.log(data,value,label)
    jQuery('#pareto').show()
    Highcharts.chart('pareto', {
        chart: {
            renderTo: 'pareto',
            type: 'column'
        },
        title: {
            text: 'Pareto Chart'
        },
        tooltip: {
            shared: true
        },
        xAxis: {
            categories: label,
            crosshair: true
        },
        yAxis: [{
            title: {
                text: ''
            }
        }, {
            title: {
                text: ''
            },
            minPadding: 0,
            maxPadding: 0,
            max: 100,
            min: 0,
            opposite: true,
            labels: {
                format: "{value}%"
            }
        }],
        series: [{
            type: 'pareto',
            name: 'Pareto',
            yAxis: 1,
            zIndex: 10,
            baseSeries: 1,
            tooltip: {
                valueDecimals: 2,
                valueSuffix: '%'
            }
        }, {
            name: 'score',
            type: 'column',
            zIndex: 2,
            data: value
        }]
    });
}
