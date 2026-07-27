<html>
  <title>Settalment</title>
    <head>
        <style>
.etable {
    width: 100%;
    display: table;
    table-layout: fixed; /* optional, for equal spacing */
    border-collapse: collapse;
}
.etable th {
    display: table-cell;
    text-align: center;
    border: 1px solid pink;
    vertical-align: middle;
}


.highcharts-figure, .highcharts-data-table table {
    min-width: 310px; 
    max-width: 800px;
    margin: 1em auto;
}

#container {
    height: 400px;
}

.highcharts-data-table table {
	font-family: Verdana, sans-serif;
	border-collapse: collapse;
	border: 1px solid #EBEBEB;
	margin: 10px auto;
	text-align: center;
	width: 100%;
	max-width: 500px;
}
.highcharts-data-table caption {
    padding: 1em 0;
    font-size: 1.2em;
    color: #555;
}
.highcharts-data-table th {
	font-weight: 600;
    padding: 0.5em;
}
.highcharts-data-table td, .highcharts-data-table th, .highcharts-data-table caption {
    padding: 0.5em;
}
.highcharts-data-table thead tr, .highcharts-data-table tr:nth-child(even) {
    background: #f8f8f8;
}
.highcharts-data-table tr:hover {
    background: #f1f7ff;
}


        body {
            margin: 0 auto;
            padding: 0;
            font:normal 14px Arial, Helvetica, sans-serif;color:hsl(0, 0%, 29%);
            background: #fafafa;
            box-sizing: border-box;
        }
       .list{
    width: 100%;
    display: table;
    table-layout: fixed;
    border-collapse: collapse;

}
.new-table td,th{
  padding: 5px;
  vertical-align: middle;
  font-size: 12px;
}
.exceptional tr:nth-child(even) {
    background-color: #f2f2f2;
}
.table td{
    padding: 15px;
    line-height: 2;
    
    vertical-align: top;

    letter-spacing: 1;
}
table thead th{
    background: #1d54c3;
    color: #fff;
    font-size: 12px;
    margin: 0px;
    padding: 10px;
    }
   .exceptional td,th{text-align:center;padding:10px; font-size: 14px;}
.page-active{ color: white; background: #930e29; border: 0;}
.page-active a{color: #fff;}
#main a {text-decoration: none;}
.list-td {
    display: table-cell;
    text-align: center;
    vertical-align: middle;
    word-wrap: break-word;
}
        .main-wrap{
            width: 1200px;
            height: auto;
            margin: 90px auto 50px;
            background: #fff;

        }
        .active{
            color: #1e5481;
        }
        #main div {
            flex-grow: 0;
            flex-shrink: 0;
            flex-basis: 100px;
            }

            #main div:nth-of-type(2) {
            flex-basis: calc(100% - 180px);
            }
        .heading-wrap{  margin:40px auto; background: #4E5460; box-sizing: border-box;  font-size: 20px; padding: 20px; text-align: center; width: 100%; color: #fff;}

        .pageA4 {
            height: 297mm;
            margin:auto;
        }
        </style>
    </head>
    <body >
      <div style="    display: flex;
      position: fixed;
      align-items: center;
      justify-content: space-between;
      background: #fff;
      width: 100%;
      top: 0;
      z-index: 1;">

        <div style="display: flex; align-items: center;">
            <img src="{{ URL::asset('pdfImage/logo.jpg') }}" class="" alt="" style="height:  60px;"/>
           
        </div>
        <div style="display: flex; align-items: center;">
          <div class="" style="display: flex; margin: 0px 10px;"><a href=""><strong> Discard</strong></a></div>
          <div class="" style="display: flex; margin: 0px 10px;"><a href="" style="background: #930e29; padding: 10px 30px; border-radius: 20px; color: #fff; text-decoration: none;"><strong>Save</strong></a></div>
            
          <div class="" style="display: flex; margin: 0px 10px;"><a href="" style="background: yellowgreen; padding: 10px 30px; border-radius: 20px; color: #fff; text-decoration: none;"><strong>Generate PDF</strong></a></div>
            
        </div>                
    </div>
        <div class="main-wrap">
            <!-- Header -->
            
          

            <hr style="margin:0; border: 0; border-bottom: 1px solid #cccd;"/>         
            <!-- Header -->
            <div class="" id="main" style="display:block;">            
                
                <div class="right" style="background: #cccd; padding: 20px;">                    
                   <div style="padding-left: 15px; background: #fff;"> <div style="background: #fff; padding: 15px; border-left: 20px solid brown;">
                    <div class="" style="margin-bottom: 15px;">
                      <img src="{{ URL::asset('pdfImage/logo.jpg') }}" class="" alt="" style="height:  50px;"/>
                      <img src="{{ URL::asset('pdfImage/logo-qdegrees.png') }}" class="" alt="" style="height:  50px; float: right;"/>
                    </div>
                    <img src="{{ URL::asset('pdfImage/background.jpg') }}" alt="" style="width: 100%;" class=""/>
                    @if(!empty($getBranch) && !empty($getState))
                    <h3 contenteditable="true" align="right">{{$getState->name}} {{$getBranch->name}} AUDIT REPORT–WAVE-{{$quarter}} [FY {{$year}}]</h3>
                    @else
                     <h3 contenteditable="true" align="right">KARNATAKA BANGALORE AUDIT REPORT–WAVE–VII- Q3_OCT [FY 2020-21]</h3>@endif
                    <div class="" style="text-align: right; margin-bottom: 100px;">
                        <br/>
                        <br/>
                    <?php if(isset($getBranch->created_at)){
                        $getdate=explode('-',$getBranch->created_at);
                        dd(explode(' ',$getdate[2]));
                        ?>
                     
                    <h4 contenteditable="true">CODE: 202010-Q3-14-OCT-BANGALORE</h4><?php }?> 
                  
                    <br/>
                    <br/>
                    <h4 contenteditable="true">PRESENTED BY: QDEGREES SERVICES</h4>  
                    </div>

                    <h4 style="padding-left: 30px ;"><em>Private & Confidential</em></h4>
                    </div>
                </div>                </div>
            </div>
            

        </div>
        

        <div class="main-wrap">
            
            <!-- Header -->
    

            <hr style="margin:0; border: 0; border-bottom: 1px solid #cccd;"/>         
            <!-- Header -->
            <div class="" id="main" style="display: block;">            
               

                <div class="right" style="padding: 20px;     background: #cccd;           ;               ">                    
                   <div style="padding-left: 15px; background: #fff;"> <div style="background: #fff; padding: 15px; border-left: 20px solid brown;">
                    <div class="" style="margin-bottom: 15px;">
                      <img src=" {{ URL::asset('pdfImage/logo.jpg') }}" class="" alt="" style="height:  50px;"/>
                      <img src="{{ URL::asset('pdfImage/logo-qdegrees.png') }}" class="" alt="" style="height:  50px; float: right;"/>
                    </div>
                    <h3 style="color: brown; margin-bottom: 40px;" contenteditable="true"><ins>Table of Content:</ins></h3>

                    <a href="#" style="color: brown; margin-bottom: 10px;" contenteditable="true"><h3>1. Auditor’s Report- To the Stakeholders of IDFC First Limited Collection Process - <span style="float:right;">4</span></h3></a>

                    <a href="#" style="color: brown; margin-bottom: 10px;" contenteditable="true"><h3>1.1. Executive Summary - <span style="float:right;">5</span></h3></a>

                    <a href="#" style="color: brown; margin-bottom: 10px;" contenteditable="true"><h3>1.2. Gaps at Agency - <span style="float:right;">5</span></h3></a>

                    <a href="#" style="color: brown; margin-bottom: 10px;" contenteditable="true"><h3>1.2.1. Receipt cut in Non-Day light hour -<span style="float:right;">5</span></h3></a>

                    <a href="#" style="color: brown; margin-bottom: 10px;" contenteditable="true"><h3>1.2.2. Delay in Cash Deposition - <span style="float:right;">5</span></h3></a>

                    <a href="#" style="color: brown; margin-bottom: 10px;" contenteditable="true"><h3>1.2.3. Trail GAP - <span style="float:right;">5</span></h3></a>

                    <a href="#" style="color: brown; margin-bottom: 10px;" contenteditable="true"><h3>1.2.4. Delay in Secondary Allocation - <span style="float:right;">6</span></h3></a>

                    
                    <a href="#" style="color: brown; margin-bottom: 10px;" contenteditable="true"><h3>1.2.5. Account to Collector Ratio - <span style="float:right;">6</span></h3></a>

                    <a href="#" style="color: brown; margin-bottom: 10px;" contenteditable="true"><h3>1.2.6. Other Observation - <span style="float:right;">6</span></h3></a>

                    <a href="#" style="color: brown; margin-bottom: 10px;" contenteditable="true"><h3>3.1. Gaps at Branch: - <span style="float:right;">6</span></h3></a>

                    <a href="#" style="color: brown; margin-bottom: 10px;" contenteditable="true"><h3>3.2. Parameter wise scores - Wave-wise comparison - <span style="float:right;">6</span></h3></a>

                    <a href="#" style="color: brown; margin-bottom: 10px;" contenteditable="true"><h3>4. Scoring Methodology - <span style="float:right;">9</span></h3></a>

                    <a href="#" style="color: brown; margin-bottom: 10px;" contenteditable="true"><h3>5. Audit Findings – Jamshedpur - <span style="float:right;">11</span></h3></a>

                    <a href="#" style="color: brown; margin-bottom: 10px;" contenteditable="true"><h3>5.1. Jamshedpur Branch – Cash Management Audit -<span style="float:right;">12</span></h3></a>

                    
                    <a href="#" style="color: brown; margin-bottom: 10px;" contenteditable="true"><h3>5.2. Branch Jamshedpur – Product Wise - <span style="float:right;">12</span></h3></a>

                    <a href="#" style="color: brown; margin-bottom: 10px;" contenteditable="true"><h3>5.3. Branch Repo Jamshedpur- Product Wise - <span style="float:right;">12</span></h3></a>

                    <a href="#" style="color: brown; margin-bottom: 10px;" contenteditable="true"><h3>5.4. Jamshedpur Collection Agency – Product -<span style="float:right;">13</span></h3></a>

                    <a href="#" style="color: brown; margin-bottom: 10px;" contenteditable="true"><h3>5.4.1. Agency Fair Advisory and Legale - <span style="float:right;">13</span></h3></a>

                    <a href="#" style="color: brown; margin-bottom: 10px;" contenteditable="true"><h3>5.4.2. Agency Fair Advisory Legal Services - <span style="float:right;">13</span></h3></a>

                    <a href="#" style="color: brown; margin-bottom: 10px;" contenteditable="true"><h3>5.4.3. Agency Jai Sai Services - <span style="float:right;">14</span></h3></a>

                    <a href="#" style="color: brown; margin-bottom: 10px;" contenteditable="true"><h3>5.4.4. Agency MITHILA SERVICES - <span style="float:right;">14</span></h3></a>

                    <a href="#" style="color: brown; margin-bottom: 10px;" contenteditable="true"><h3>5.4.5. Agency MS KALYANI ASSOCIATES - <span style="float:right;">16</span></h3></a>

                    <a href="#" style="color: brown; margin-bottom: 10px;"><h3 contenteditable="true">5.4.6. Agency Pandey Enterprises - <span style="float:right;">16</span></h3></a>
                    <a href="#" style="color: brown; margin-bottom: 10px;"><h3 contenteditable="true">5.5. Repo Agency - <span style="float:right;">16</span></h3></a>
                    <a href="#" style="color: brown; margin-bottom: 10px;"><h3 contenteditable="true">5.5.1. Jai sai Services - <span style="float:right;">16</span></h3></a>
                    <a href="#" style="color: brown; margin-bottom: 10px;"><h3 contenteditable="true">5.5.2. MS KALYANI ASSOCIATES - <span style="float:right;">16</span></h3></a>
                    <a href="#" style="color: brown; margin-bottom: 10px;"><h3 contenteditable="true">5.6. Yard Agency – Product Wise - <span style="float:right;">17</span></h3></a>
                    <a href="#" style="color: brown; margin-bottom: 10px;"><h3 contenteditable="true">5.6.1. MS RAJKISHORE YADAV - <span style="float:right;">17</span></h3></a>
                    <a href="#" style="color: brown; margin-bottom: 10px;"><h3 contenteditable="true">6. Audit Findings – Ranchi - <span style="float:right;">17</span></h3></a>
                    <a href="#" style="color: brown; margin-bottom: 10px;"><h3 contenteditable="true">6.1. Ranchi Branch – Cash Management Audit - <span style="float:right;">17</span></h3></a>
                    <a href="#" style="color: brown; margin-bottom: 10px;"><h3 contenteditable="true">6.2. Branch Ranchi– Product Wise - <span style="float:right;">18</span></h3></a>
                    <a href="#" style="color: brown; margin-bottom: 10px;"><h3 contenteditable="true">6.3. Branch Repo Ranchi- Product Wise - <span style="float:right;">18</span></h3></a>
                    <a href="#" style="color: brown; margin-bottom: 10px;"><h3 contenteditable="true">6.4. Ranchi Collection Agency – Product Wise - <span style="float:right;">19</span></h3></a>
                    <a href="#" style="color: brown; margin-bottom: 10px;"><h3 contenteditable="true">6.4.1. Agency FAIR ADVISORY AND LEGALE - <span style="float:right;">19</span></h3></a>
                    <a href="#" style="color: brown; margin-bottom: 10px;"><h3 contenteditable="true">6.4.2. Agency Global Informatics - <span style="float:right;">19</span></h3></a>
                    <a href="#" style="color: brown; margin-bottom: 10px;"><h3 contenteditable="true">6.4.3. Agency Jai sai Services - <span style="float:right;">20</span></h3></a>
                    <a href="#" style="color: brown; margin-bottom: 10px;"><h3 contenteditable="true">6.4.4. Agency Siddhi Vinayaka - <span style="float:right;">21</span></h3></a>


                    <a href="#" style="color: brown; margin-bottom: 10px;"><h3  contenteditable="true">6.5. Repo Agency - <span style="float:right;">21</span></h3></a>
                    <a href="#" style="color: brown; margin-bottom: 10px;"><h3 contenteditable="true">6.5.1. Jai sai Services - <span style="float:right;">21</span></h3></a>
                    <a href="#" style="color: brown; margin-bottom: 10px;"><h3 contenteditable="true">6.6. Yard Agency – Product Wise - <span style="float:right;">21</span></h3></a>
                    <a href="#" style="color: brown; margin-bottom: 10px;"><h3 contenteditable="true">6.6.1. Kuldip Automobiles - <span style="float:right;">21</span></h3></a>
                    <a href="#" style="color: brown; margin-bottom: 10px;"><h3 contenteditable="true">7. Score Card – Product-Wise - <span style="float:right;">22</span></h3></a>
                    <a href="#" style="color: brown; margin-bottom: 10px;"><h3 contenteditable="true">8. Score Card – Collection Manager - <span style="float:right;">22</span></h3></a>
                    <a href="#" style="color: brown; margin-bottom: 10px;"><h3 contenteditable="true">9. Score Card – Collection Agency/Repo/Yard Agency – Product-Wise - <span style="float:right;">23</span></h3></a>
                    <a href="#" style="color: brown; margin-bottom: 10px;"><h3 contenteditable="true">10. Annexure - <span style="float:right;">24</span></h3></a>
                    <a href="#" style="color: brown; margin-bottom: 10px;"><h3 contenteditable="true">10.1. List of Branches Audited - <span style="float:right;">24</span></h3></a>
                    <a href="#" style="color: brown; margin-bottom: 10px;"><h3 contenteditable="true">10.2. List of Collection Agencies Audited - <span style="float:right;">24</span></h3></a>
                    <a href="#" style="color: brown; margin-bottom: 10px;"><h3 contenteditable="true">10.3. Audit Team - <span style="float:right;">25</span></h3></a>
                    <a href="#" style="color: brown; margin-bottom: 10px;"><h3 contenteditable="true">10.4. Visiting Details - <span style="float:right;">25</span></h3></a>
                    <a href="#" style="color: brown; margin-bottom: 10px;"><h3 contenteditable="true">10.5. Abbreviations - <span style="float:right;">27</span></h3></a>
                  
                    <h3 style="color: brown; margin-bottom: 40px; margin-top: 40px;"><ins>List of Tables:</ins> </h3>
                    
                    <a href="#" style="color: blue; margin-bottom: 10px;"><h3  contenteditable="true">Table 1: Jharkhand-Branch & Collection/Yard Agency – Compliance - <span style="float:right;">5</span></h3></a>
                    <a href="#" style="color: blue; margin-bottom: 10px;"><h3 contenteditable="true">Table 2: Receipt cut in Non-Day light Hour - <span style="float:right;">5</span></h3></a>
                    <a href="#" style="color: blue; margin-bottom: 10px;"><h3 contenteditable="true">Table 3: Delay in Cash Deposition - <span style="float:right;">5</span></h3></a>
                    <a href="#" style="color: blue; margin-bottom: 10px;"><h3 contenteditable="true">Table 4: Trail Gap (No attempt on allocated accounts) - <span style="float:right;">5</span></h3></a>
                    <a href="#" style="color: blue; margin-bottom: 10px;"><h3 contenteditable="true">Table 5: Delay in Secondary Allocation - <span style="float:right;">6</span></h3></a>
                    <a href="#" style="color: blue; margin-bottom: 10px;"><h3 contenteditable="true">Table 5: Account to Collector Ratio (ACR)-FOS Wise - <span style="float:right;">6</span></h3></a>
                    <a href="#" style="color: blue; margin-bottom: 10px;"><h3 contenteditable="true">Table 7 : FAIR ADVISORY AND LEGALE - Product-Wise – Key Issues - <span style="float:right;">13</span></h3></a>
                    <a href="#" style="color: blue; margin-bottom: 10px;"><h3 contenteditable="true">Table 8 : Fair Advisory Legal Services - Product-Wise – Key Issues - <span style="float:right;">13</span></h3></a>
                    <a href="#" style="color: blue; margin-bottom: 10px;"><h3 contenteditable="true">Table 9 : Jai Sai Services - Product-Wise – Key Issues - <span style="float:right;">14</span></h3></a>
                    <a href="#" style="color: blue; margin-bottom: 10px;"><h3 contenteditable="true">Table 10 : MITHILA SERVICES - Product-Wise – Key Issues - <span style="float:right;">14</span></h3></a>
                    <a href="#" style="color: blue; margin-bottom: 10px;"><h3 contenteditable="true">Table 11 : MS KALYANI ASSOCIATES - Product-Wise – Key Issues - <span style="float:right;">16</span></h3></a>
                    <a href="#" style="color: blue; margin-bottom: 10px;"><h3 contenteditable="true">Table 12 : Pandey Enterprises - Product-Wise – Key Issues - <span style="float:right;">16</span></h3></a>
                    <a href="#" style="color: blue; margin-bottom: 10px;"><h3 contenteditable="true">Table 13: Jai Sai Services - Product-Wise – Key Issues - <span style="float:right;">16</span></h3></a>
                    <a href="#" style="color: blue; margin-bottom: 10px;"><h3 contenteditable="true">Table 14: MS KALYANI ASSOCIATES - Product-Wise – Key Issues - <span style="float:right;">16</span></h3></a>
                    <a href="#" style="color: blue; margin-bottom: 10px;"><h3 contenteditable="true">Table 15: MS RAJKISHORE YADAV- Product-Wise – Key Issues - <span style="float:right;">17</span></h3></a>
                    <a href="#" style="color: blue; margin-bottom: 10px;"><h3 contenteditable="true">Table 16: FAIR ADVISORY AND LEGALE - Product-Wise – Key Issues - <span style="float:right;">19</span></h3></a>
                    <a href="#" style="color: blue; margin-bottom: 10px;"><h3 contenteditable="true">Table 17: Global Informatics - Product-Wise – Key Issues - <span style="float:right;">19</span></h3></a>
                    <a href="#" style="color: blue; margin-bottom: 10px;"><h3 contenteditable="true">Table 18: Jai Sai Services - Product- Wise – Key Issues - <span style="float:right;">20</span></h3></a>
                    <a href="#" style="color: blue; margin-bottom: 10px;"><h3 contenteditable="true">Table 19: Jai Sai Services - Product-Wise – Key Issues - <span style="float:right;">21</span></h3></a>
                    <a href="#" style="color: blue; margin-bottom: 10px;"><h3  contenteditable="true">Table 20: Kuldip Automobiles- Product-Wise – Key Issues - <span style="float:right;">21</span></h3></a>
                    <a href="#" style="color: blue; margin-bottom: 10px;"><h3 contenteditable="true">Table 21: Jharkhand-Ranchi- Branch Score Card Product-Wise - <span style="float:right;">22</span></h3></a>
                    <a href="#" style="color: blue; margin-bottom: 10px;"><h3 contenteditable="true">Table 22: Jharkhand-Jamshedpur- Branch Score Card Product-Wise - <span style="float:right;">22</span></h3></a>
                    <a href="#" style="color: blue; margin-bottom: 10px;"><h3 contenteditable="true">Table 23: Jharkhand – Ranchi - Collection Manager Score Card - <span style="float:right;">22</span></h3></a>
                    <a href="#" style="color: blue; margin-bottom: 10px;"><h3 contenteditable="true">Table 24: Jharkhand- Jamshedpur - Collection Manager Score Card - <span style="float:right;">22</span></h3></a>
                    <a href="#" style="color: blue; margin-bottom: 10px;"><h3 contenteditable="true">Table 25: Jharkhand – Ranchi – Collection /Repo/ Yard Agency - Score Card – Product -Wise - <span style="float:right;">23</span></h3></a>
                    <a href="#" style="color: blue; margin-bottom: 10px;"><h3 contenteditable="true">Table 26: Jharkhand – Jamshedpur– Collection Agency/Repo/Yard Agency Score Card – Product - Wise - <span style="float:right;">23</span></h3></a>


                    <a href="#" style="color: blue; margin-bottom: 10px;"><h3 contenteditable="true">Table 27: Jharkhand - List of Branches Covered - <span style="float:right;">24</span></h3></a>
                    <a href="#" style="color: blue; margin-bottom: 10px;"><h3 contenteditable="true">Table 28: Jharkhand - List of Collection Agencies - <span style="float:right;">25</span></h3></a>
                    <a href="#" style="color: blue; margin-bottom: 10px;"><h3 contenteditable="true">Table 29: Qdegrees Audit Team - <span style="float:right;">25</span></h3></a>
                    <a href="#" style="color: blue; margin-bottom: 10px;"><h3 contenteditable="true">Table 30: Jharkhand- Qdegrees Team Audit Visiting Details - <span style="float:right;">25</span></h3></a>
                    <a href="#" style="color: blue; margin-bottom: 10px;"><h3 contenteditable="true">Table 31: Abbreviations - <span style="float:right;">27</span></h3></a>
                    
                    <h3 style="color: brown; margin-bottom: 40px; margin-top: 40px;"  contenteditable="true"><ins>List of Figures:</ins> </h3>
                    
                    <a href="#" style="color: blue; margin-bottom: 10px;"><h3  contenteditable="true">Figure 1: Collection Team – Scoring Methodology - <span style="float:right;">9</span></h3></a>
                  
                    <h4 style="padding-left: 30px ;"><em>Private & Confidential</em></h4>
                    </div>
                </div>                </div>
            </div>

         
            
           

            

            

     


            

        </div>

        <div class="main-wrap">
          
     
            <hr style="margin:0; border: 0; border-bottom: 1px solid #cccd;"/>         
            <!-- Header -->
            <div class="" id="main" style="display: block;">            
                
                <div class="right" style="padding: 20px;     background: #cccd;       ">                    
                   <div style="padding-left: 15px; background: #fff;"> 
                        <div style="background: #fff; padding: 15px; border-left: 20px solid brown;">
                            <div class="" style="margin-bottom: 15px;">
                              <img src="logo.jpg" class="" alt="" style="height:  50px;"/>
                              <img src="logo-qdegrees.png" class="" alt="" style="height:  50px; float: right;"/>
                            </div>
                            <h1 >Executive Summary</h1>
                            <h3 style="color: darkblue;">1. Auditor’s Report- To the Stakeholders of IDFC First Limited Collection Process</h3>

                            <table class="table" style="margin: 70px 0px;">
                                <tbody>
                                    <tr>
                                        <td  contenteditable="true">1. Audit was conducted in accordance to the auditing parameters agreed upon covering internal compliances and banking guidelines. The audit includes checking the collection process as per the SOP and identifying gaps at each stage</td>
                                        <td  contenteditable="true">3. Visits were made at the Branch, and Collection Agencies. Audits conducted on agreed checklist and findings were captured in the online tool along with proper artefacts in form of pictures </td>
                                    </tr>
                                    <tr>
                                        <td  contenteditable="true">2. The audit was initiated in the state of Jharkhand from 10th Feb 2020 to 19th Feb 2020. 2 branches and 8 agencies were covered and total 58 audits were done across all products. Data considered for audit is from Nov 2019 to Jan 2020</td>
                                        
                                        <td  contenteditable="true">4. Key objective of the Audit is to identify the gaps in the Collections process at Branch, Agency and yard. Gaps in Review and evaluation of the collection manager performance in the IDFC First Branch on the different key performance parameters</td>
                                    </tr>
                                </tbody>
                            </table>
                            <table style="width: 100%; color: darkgoldenrod; font-weight: 500;">
                                <tbody>
                                    <tr>
                                        <td>Private & Confidential</td>
                                        <td>Karnataka Bangalore Audit Report–Wave–VII- Q3_OCT [FY 2020-21]</td>
                                        <td>4</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>                
                </div>
            </div>

         
            
           

            

            

     


            

        </div>













        <div class="main-wrap">
            
            <!-- Header -->
            
           

            <hr style="margin:0; border: 0; border-bottom: 1px solid #cccd;"/>         
            <!-- Header -->
            <div class="" id="main" style="display: block;">            
                
                <div class="right" style="padding: 20px;     background: #cccd;        ">                    
                   <div style="padding-left: 15px; background: #fff;"> <div style="background: #fff; padding: 15px; border-left: 20px solid brown;">            
                    <div class="" style="position: relative;padding-bottom:45px;margin-bottom: 28px; background:#fff; margin-bottom: 50px;">
                        <div class="" style="margin-bottom: 15px;">
                          <img src="logo.jpg" class="" alt="" style="height:  50px;"/>
                          <img src="logo-qdegrees.png" class="" alt="" style="height:  50px; float: right;"/>
                        </div>
                        <h1 style="font-weight: normal; color: #1d54c3;"  contenteditable="true">1.1 Executive Summary</h1>
                        <h4  contenteditable="true">The overall compliance of Jharkhand, including branch and Collection/Repo/Yard agency are given in the below table:</h4>
                        
                        <figure class="highcharts-figure">
                          <div id="container"></div>                       
                        </figure>
                        
                        
                        <table class="exceptional" width="100%" style="margin-bottom: 100px; text-align: left;">
                            <thead>
                              <tr>
                                <th colspan="8">Table 1: Karnataka- Bangalore – Product wise Score Overall – Compliance</th>
                              </tr>
                         
                            </thead>
                            <tbody>
                              <tr>
                                <th  contenteditable="true">S.No.</th>
                                <th contenteditable="true">Product </th>
                                <th contenteditable="true">Branch  </th>
                                <th contenteditable="true">Collection Agency/Repo/Yard</th>
                                <th contenteditable="true">Overall Wave-IV</th>
                                <th contenteditable="true">Indicator</th>
                                <th contenteditable="true">Overall Wave-III  </th>                      
                                <th contenteditable="true">Overall Wave-II</th>                            
                              </tr>
                              <tr>
                                <td contenteditable="true" class="red">1</td>
                                <td  contenteditable="true"class="red"> Auto-Used Car </td>
                                <td contenteditable="true" class="red">77.4%</td>
                                <td  contenteditable="true"class="red">87.7% </td>
                                <td contenteditable="true" class="red">82.7%</td>
                                <td contenteditable="true"><img src="arrow-down.png" style="height:20px;"/></td>                   
                                <td contenteditable="true"> 90.2%</td>
                                <td contenteditable="true"> 55.8%</td>    
                              </tr>
                              <tr>
                                <td contenteditable="true">2</td>
                                <td contenteditable="true">Business Loan</td>
                                <td contenteditable="true">78.3%</td>
                                <td contenteditable="true">NA</td>
                                <td contenteditable="true">34.7%</td>
                                <td contenteditable="true"><img src="arrow-down.png" style="height:20px;"/></td>
                                <td contenteditable="true">78.3%</td>
                                <td contenteditable="true">NA</td>
                              </tr>
                              <tr>
                                <td contenteditable="true">3</td>
                                <td contenteditable="true">Consumer Loan</td>
                                <td contenteditable="true">47.4%</td>
                                <td contenteditable="true">78.1%</td>

                                <td contenteditable="true">34.7%</td>
                                <td contenteditable="true"><img src="arrow-down.png" style="height:20px;"/></td>
                                <td contenteditable="true"> 81.9% </td>
                              
                                <td contenteditable="true">66.6% </td>
                    
                              </tr>
                              <tr>
                                <td contenteditable="true">4</td>
                                <td contenteditable="true">Personal Loan</td>
                                <td contenteditable="true">68.1%</td>
                                <td contenteditable="true">81.3%</td>

                                <td  contenteditable="true">77.9%</td>
                                <td  contenteditable="true"><img src="arrow-down.png" style="height:20px;"/></td>
                                <td  contenteditable="true"> 85.0% </td>
                      
                                <td  contenteditable="true">68.0% </td>
                   
                              </tr>
                              <tr>
                                <td contenteditable="true">5</td>
                                <td contenteditable="true">Two-Wheeler</td>
                                <td contenteditable="true">75.6%</td>
                                <td contenteditable="true">80.5%</td>

                                <td contenteditable="true">79.3%</td>
                                <td  contenteditable="true"><img src="arrow-up.png" style="height:20px;"/></td>
                                <td contenteditable="true"> 80.4% </td>
                        
                                <td contenteditable="true">64.8% </td>
                   
                              </tr>
                              
                              <tr>
                                <td>6</td>
                                <td>Mort_HFC</td>
                                <td>75.0%</td>
                                <td>89.9%</td>

                                <td>83.2%</td>
                                <td  contenteditable="true"><img src="arrow-down.png" style="height:20px;"/></td>
                                <td> 83.7% </td>
                        
                                <td>NA</td>
                   
                              </tr>
                              <tr>
                                <td>7</td>
                                <td>Micro Business Loan</td>
                                <td>78.3%</td>
                                <td>89.5%</td>
                                <td>84.8%</td>                        
                                <td><img src="arrow-up.png" style="height:20px;"/></td>
                                <td>84.8%</td>
                                <td>NA</td>
                                
                   
                              </tr>
                              <tr>
                                <td class="" colspan="2">Total</td>
                               
                                <td>72.8%</td>
                                <td>83.3%</td>

                                <td>80.3%</td>
                                <td><img src="arrow-down.png" style="height:20px;"/></td>
                                <td>83.4% </td>
                        
                                <td>64.0% </td>
                   
                              </tr>
                            </tbody>
                        </table>

                        <h4>Key Issues at branch:</h4>
                        <ul>
                          <li>Agency performance report not shared by Collection Manager-Vijay Hullikeri (Product-CL)</li>
                          <li>Endorsement card expired of 1 FOS (CM – Simon Suman N- Business Loan)</li>
                          <li>Endorsement card expired of 11 FOS (CM – ASHARANI.B.R – Cross Sell Loan)</li>
                          <li>Endorsement card expired of 5 FOS (Vinod Kumar Pal-Cross Sell Loan)</li>
                          <li>Endorsement card expired of 14 FOS (Pavangeer-Two-Wheeler Loan)</li>
                        </ul>


                        <h2 style="font-weight: normal; color: #1d54c3;">1.2. Key issues</h2>

                

                        <table class="exceptional" width="100%" style="text-align: left;">
                            <thead>
                              <tr>
                                <th colspan="6">Table 2: Receipt cut in Non-Day light Hour</th>                                                   
                              </tr>
                              <tr style="font-weight: bolder;">
                                <td>S.No.</td>                                                   
                                <td>Gaps</td>                                                   
                                <td>Count</td>                                                   
                                <td>Receipt Amount (Rs)</td>                
                                <td>POS Value (Cr.)</td>                                                   
                                <td>Touch Point</td>                                                                                       
                            </tr>
                            </thead>
                            <tbody>
                                
                              <tr>
                                <td class="red">1</td>
                                <td class="red">Key Issues at YARD</td>
                                <td class="red">-</td>
                                <td class="red">- </td>
                                <td class="red">-</td>
                                <td class="red">8 repo Yard </td>
                              </tr>
                              <tr>
                                <td class="red">2</td>
                                <td class="red">Receipt cut in non-day light hour</td>
                                <td class="red">5</td>
                                <td class="red">16,825</td>
                                <td class="red">-</td>
                                <td class="red">Agency </td>
                              </tr>
                              <tr>
                                <td class="red">3</td>
                                <td class="red">Delayed cash deposition</td>
                                <td class="red">747</td>
                                <td class="red">53,52,571</td>
                                <td class="red">-</td>
                                <td class="red">Agency & Inhouse </td>
                              </tr>
                              <tr>
                                <td class="red">4</td>
                                <td class="red">Trail not updated</td>
                                <td class="red">25729</td>
                                <td class="red">- </td>
                                <td class="red">112.64</td>
                                <td class="red">Agency</td>
                              </tr>
                              <tr>
                                <td class="red">5</td>
                                <td class="red">Cases not allocated to FOS</td>
                                <td class="red">1981</td>
                                <td class="red">- </td>
                                <td class="red">28.32</td>
                                <td class="red">Agency & Inhouse</td>
                              </tr>
                              <tr>
                                <td class="red">6</td>
                                <td class="red">Delay in secondary allocation</td>
                                <td class="red">68203</td>
                                <td class="red">-</td>
                                <td class="red">228.20</td>
                                <td class="red">Agency</td>
                              </tr>
                              <tr>
                                <td class="red">7</td>
                                <td class="red">Account to Collector Ratio</td>
                                <td class="red">-</td>
                                <td class="red">- </td>
                                <td class="red">-</td>
                                <td class="red">Agency & Inhouse</td>
                              </tr>
                                                  
                            </tbody>
                        </table>
                       


                        <h3 style="font-weight: normal; color: #1d54c3;">1.2.1. Key issues at YARD</h3>

                        <table class="exceptional" width="100%" style="text-align: left;">
                            <thead>                       
                              <tr style="font-weight: bolder;">
                                <th>S.No.</th>                                                   
                                <th>Repo Agency Name</th>                                                   
                                <th>Collection Manager</th>                                                   
                                <th>Product</th>     
                                <th>Key Issues</th>                                                          
                            </tr>
                            </thead>
                            <tbody>
                                
                              <tr>
                                <td class="red">1</td>
                                <td class="red">Hamsa Associates</td>
                                <td class="red">Bipul Singh</td>
                                <td class="red">TW</td>
                                <td>
                                  <ul style="text-align: left;">
                                    <li>Repo agency agreement not available</li>                          
                                  </ul>
                                </td>                             
                              </tr>
                              <tr>
                                <td class="red">2</td>
                                <td class="red">Hamsa Associates</td>
                                <td class="red">Bipul Singh</td>
                                <td class="red">TW</td>
                                <td>
                                  <ul style="text-align: left;">
                                    <li>Repo agency agreement not available</li>                          
                                  </ul>
                                </td>                             
                              </tr>
                              <tr>
                                <td class="red">3</td>
                                <td class="red">Hamsa Associates</td>
                                <td class="red">Bipul Singh</td>
                                <td class="red">TW</td>
                                <td>
                                  <ul style="text-align: left;">
                                    <li>Repo agency agreement not available</li>   
                                    <li>Authorization letter not available at Repo Agency (Fresh Recovery)</li>                       
                                  </ul>
                                </td>                             
                              </tr>
                              <tr>
                                <td class="red">4</td>
                                <td class="red">Hamsa Associates</td>
                                <td class="red">Bipul Singh</td>
                                <td class="red">TW</td>
                                <td>
                                  <ul style="text-align: left;">
                                    <li>Repo agency agreement not available</li>                          
                                  </ul>
                                </td>                             
                              </tr>
                              <tr>
                                <td class="red">5</td>
                                <td class="red">Hamsa Associates</td>
                                <td class="red">Bipul Singh</td>
                                <td class="red">TW</td>
                                <td>
                                  <ul style="text-align: left;">
                                    <li>Repo agency agreement not available</li>                          
                                  </ul>
                                </td>                             
                              </tr>
                              <tr>
                                <td class="red">6</td>
                                <td class="red">Hamsa Associates</td>
                                <td class="red">Bipul Singh</td>
                                <td class="red">TW</td>
                                <td>
                                  <ul style="text-align: left;">
                                    <li>Repo agency agreement not available</li>                          
                                  </ul>
                                </td>                             
                              </tr>
                                                       
                            </tbody>
                        </table>
                        <p style="color: blue;  margin-bottom: 40px;">Note: Detailed issues mentioned on page no 64 to 67</p>



                        <h3 style="font-weight: normal; color: #1d54c3;">1.2.2. Receipt cut in Non-Day Light Hour</h3>
                        <table class="exceptional" width="100%" style="text-align: left;">
                            <thead>
                              <tr>
                                <th colspan="4">Table 2: Receipt cut in Non-Day light Hour</th>                                                   
                              </tr>
                              <tr style="font-weight: bolder;">
                                <td>S.No.</td>                                                   
                                <td>Count</td>                                                   
                                <td>Total Receipt Amount</td>                                                   
                                <td>Payment Mode</td>                                          
                            </tr>
                            </thead>
                            <tbody>
                                
                              <tr>
                                <td class="red">1</td>
                                <td class="red"> 5 </td>
                                <td class="red">16,825</td>
                                <td>Cash</td>
                              </tr>       
                            </tbody>
                        </table>
                        <ul>
                          <li>290 receipts delay deposited by INHOUSE BANGALORE in May, Jun & Jul Month amounting to Rs.31.47 Lacs</li>
                          <li>132 receipts delay deposited by CSR VENTURE in May, Jun & Jul month amounting to Rs 3.84 Lacs</li>
                          <li>64 receipts delay deposited by DIVYA ASSOCIATES BANGALORE in Jun and Jul month of 1.71 Lacs</li>
                          <li>47 receipts delay deposited by SAP ASSOCIATES in May, Jun & Jul month of 1.42 Lacs</li>
                          <li>41 receipts delay deposited by S K ASSOCIATES in Jun and Jul month of 1.54 Lacs</li>
                        </ul>
                        <h3 style="color: brown;  margin-bottom: 40px;">#Data Source: DAC File</h3>




                        <table style="width: 100%; color: darkgoldenrod; text-align:left; font-weight:  500;">
                            <tbody>
                                <tr>
                                    <td>Private & Confidential</td>
                                    <td>Karnataka Bangalore Audit Report–Wave–VII- Q3_OCT [FY 2020-21]</td>
                                    <td>4</td>
                                </tr>
                            </tbody>
                        </table>



                    </div>
                </div>                </div>
            </div>

         
            
           

            

            

     


            

        </div>
        </div>



        
        <div class="main-wrap">
            
            <!-- Header -->
            
           

            <hr style="margin:0; border: 0; border-bottom: 1px solid #cccd;"/>         
            <!-- Header -->
            <div class="" id="main" style="display:block; ">            
               
                <div class="right" style="padding: 20px;     background: #cccd;                 ">                    
                   <div style="padding-left: 15px; background: #fff;"> <div style="background: #fff; padding: 15px; border-left: 20px solid brown;">            
                    <div class="" style="position: relative;padding-bottom:45px;margin-bottom: 28px; background:#fff; margin-bottom: 50px;">
                      <div class="" style="margin-bottom: 15px;">
                        <img src="logo.jpg" class="" alt="" style="height:  50px;"/>
                        <img src="logo-qdegrees.png" class="" alt="" style="height:  50px; float: right;"/>
                      </div>
                       

                        <h3 style="font-weight: normal; color: #1d54c3;">1.2.3. Delayed Cash Deposition</h3>
                        <table class="exceptional" width="100%" style="text-align: left;">
                            <thead>
                              <tr>
                                <th colspan="4">Table 2: Delayed Cash Deposition</th>                                                   
                              </tr>
                              <tr style="font-weight: bolder;">
                                <td>S.No.</td>                                     
                                <td>Count</td>                                                   
                                <td>Total Receipt Amount (Lacs)</td>             
                                <td>Delay in Days</td>                                       
                            </tr>
                            </thead>
                            <tbody>
                                
                              <tr>
                                <td class="red">1</td>                           
                                <td class="red">640</td>
                                <td class="red">43.47</td>
                                <td class="red"> 3 To 5 Days</td>
                              </tr>
                              <tr>
                                <td class="red">2</td>                           
                                <td class="red">640</td>
                                <td class="red">43.47</td>
                                <td class="red"> 3 To 5 Days</td>
                              </tr>
                              <tr>
                                <td class="red">3</td>                           
                                <td class="red">640</td>
                                <td class="red">43.47</td>
                                <td class="red"> 3 To 5 Days</td>
                              </tr>
                              <tr>
                                <td class="red">4</td>                           
                                <td class="red">640</td>
                                <td class="red">43.47</td>
                                <td class="red"> 3 To 5 Days</td>
                              </tr>
                             
                            </tbody>
                        </table>



                        <ul>
                          <li>290 receipts delay deposited by INHOUSE BANGALORE in May, Jun & Jul Month amounting to Rs.31.47 Lacs</li>
                          <li>132 receipts delay deposited by CSR VENTURE in May, Jun & Jul month amounting to Rs 3.84 Lacs</li>
                          <li>64 receipts delay deposited by DIVYA ASSOCIATES BANGALORE in Jun and Jul month of 1.71 Lacs</li>
                          <li>47 receipts delay deposited by SAP ASSOCIATES in May, Jun & Jul month of 1.42 Lacs</li>
                          <li>41 receipts delay deposited by S K ASSOCIATES in Jun and Jul month of 1.54 Lacs</li>
                        </ul>
                   

                        <h3 style="color: brown; margin-bottom: 40px;">#Data Source : DAC File</h3>

                        <h3 style="font-weight: normal; color: #1d54c3;">1.2.4. Trail GAP</h3>

                        <table class="exceptional" width="100%" style="margin-bottom:30px; text-align: left;">
                          <thead>
                            <tr>
                              <th colspan="4">Table 3: Trail Gap (No attempt on allocated accounts)</th>                                                   
                            </tr>
                            <tr style="font-weight: bolder;">
                              <td>S.No.</td>                                                   
                              <td>Count</td>                                                   
                              <td>POS Amount (Cr.)</td>                                                   
                                                               
                          </tr>
                          </thead>
                          <tbody>
                              
                            <tr>
                              <td class="red">1</td>
                              <td class="red"> 1250 </td>
                              <td class="red">6.25 Cr.</td>
          
                            </tr>
                            <tr>
                              <td></td>
                              <td>Total</td>
                              <td>6.25 Cr.</td>

                            </tr>                           
                          </tbody>
                      </table>

                      <p><strong><em>Top contributing agencies:</em></strong></p>

                      <table class="exceptional" width="100%" style="margin-bottom:30px; text-align: left;">
                        <thead>
                         
                          <tr style="font-weight: bolder;">
                            <th>Agency Name</th>                                                   
                            <th>May'20</th>                                                   
                            <th>Jun'20</th>                                                   
                            <th>Jul'20</th>
                            <th>Grand Total</th>
                            <th>Contribution %</th>                    
                        </tr>
                        </thead>
                        <tbody>
                            
                          <tr>
                            <td class="red">S S ASSOCIATES</td>
                            <td class="red">27 </td>
                            <td class="red">3119</td>
                            <td class="red">54</td>
                            <td class="red">3200 </td>
                            <td class="red">12%</td>        
                          </tr>
                          <tr>
                            <td class="red">IMPACT SERVICES</td>
                            <td class="red">27 </td>
                            <td class="red">3119</td>
                            <td class="red">54</td>
                            <td class="red">3200 </td>
                            <td class="red">12%</td>        
                          </tr>
                          <tr>
                            <td class="red">ROYAL SERVICES 3201370</td>
                            <td class="red">27 </td>
                            <td class="red">3119</td>
                            <td class="red">54</td>
                            <td class="red">3200 </td>
                            <td class="red">12%</td>        
                          </tr>
                          <tr>
                            <td class="red">SRI GIRI ASSOCIATES</td>
                            <td class="red">27 </td>
                            <td class="red">3119</td>
                            <td class="red">54</td>
                            <td class="red">3200 </td>
                            <td class="red">12%</td>        
                          </tr>
                          <tr>
                            <td class="red">VISESH CREDIT SERVICES</td>
                            <td class="red">27 </td>
                            <td class="red">3119</td>
                            <td class="red">54</td>
                            <td class="red">3200 </td>
                            <td class="red">12%</td>        
                          </tr> 
                                              
                        </tbody>
                    </table>
                    <ul>
                      <li>Major allocation gap contributed by SURYA ASSOCIATES i.e. 33% of overall allocation gap at Bangalore</li>
                    </ul>

                    <h4 style="color: brown; margin-bottom: 40px;">#Data Source: Trail Intensity_MIS</h4>

                    <h3 style="font-weight: normal; color: #1d54c3; margin-top: 40px;">1.2.5. Allocation GAP</h3>

                    <table class="exceptional" width="100%" style="margin-bottom:30px; text-align: left;">
                      <thead>
                        <tr>
                          <th colspan="4">Table 3: Trail Gap (No attempt on allocated accounts)</th>                                                   
                        </tr>
                        <tr style="font-weight: bolder;">
                          <td>S.No.</td>                                                   
                          <td>Count</td>                                                   
                          <td>POS Amount (Cr.)</td>                                                   
                                                           
                      </tr>
                      </thead>
                      <tbody>
                          
                        <tr>
                          <td class="red">1</td>
                          <td class="red"> 1250 </td>
                          <td class="red">6.25 Cr.</td>
      
                        </tr>
                        <tr>
                          <td></td>
                          <th>Total</th>
                          <th>6.25 Cr.</th>

                        </tr>                           
                      </tbody>
                  </table>

                  <p><strong><em>Top contributing agencies:</em></strong></p>

                  <table class="exceptional" width="100%" style="margin-bottom:30px; text-align: left;">
                    <thead>
                     
                      <tr style="font-weight: bolder;">
                        <th>Agency Name</th>                                                   
                        <th>May'20</th>                                                   
                        <th>Jun'20</th>                                                   
                        <th>Jul'20</th>
                        <th>Grand Total</th>
                        <th>Contribution %</th>                    
                    </tr>
                    </thead>
                    <tbody>
                        
                      <tr>
                        <td class="red">S S ASSOCIATES</td>
                        <td class="red">27 </td>
                        <td class="red">3119</td>
                        <td class="red">54</td>
                        <td class="red">3200 </td>
                        <td class="red">12%</td>        
                      </tr>
                      <tr>
                        <td class="red">IMPACT SERVICES</td>
                        <td class="red">27 </td>
                        <td class="red">3119</td>
                        <td class="red">54</td>
                        <td class="red">3200 </td>
                        <td class="red">12%</td>        
                      </tr>
                      <tr>
                        <td class="red">ROYAL SERVICES 3201370</td>
                        <td class="red">27 </td>
                        <td class="red">3119</td>
                        <td class="red">54</td>
                        <td class="red">3200 </td>
                        <td class="red">12%</td>        
                      </tr>
                      <tr>
                        <td class="red">SRI GIRI ASSOCIATES</td>
                        <td class="red">27 </td>
                        <td class="red">3119</td>
                        <td class="red">54</td>
                        <td class="red">3200 </td>
                        <td class="red">12%</td>        
                      </tr>
                      <tr>
                        <td class="red">VISESH CREDIT SERVICES</td>
                        <td class="red">27 </td>
                        <td class="red">3119</td>
                        <td class="red">54</td>
                        <td class="red">3200 </td>
                        <td class="red">12%</td>        
                      </tr> 
                                          
                    </tbody>
                </table>
                <ul>
                  <li>Major trail gap contributed by S S ASSOCIATES & Impact Services i.e. 12% of overall trail gap at Bangalore</li>
                </ul>
                <h4 style="color: brown; margin-bottom: 40px;">#Data Source: Collector Allocation GAP</h4>




                    <h3 style="font-weight: normal; color: #1d54c3; margin-top: 40px;">1.2.6. Delay in Secondary Allocation</h3>
                    <table class="exceptional" width="100%" style="text-align: left; margin-bottom: 40px;">
                        <thead>
                          <tr>
                            <th colspan="4">Table 5: Delay in Secondary Allocation</th>                                                   
                          </tr>
                          <tr style="font-weight: bolder;">
                            <td>S.No.</td>                                     
                            <td>Count</td>                                                   
                            <td>POS Amount (Cr.)</td>             
                            <td>Delay in Days</td>                                       
                        </tr>
                        </thead>
                        <tbody>
                            
                          <tr>
                            <td class="red">1</td>                           
                            <td class="red">640</td>
                            <td class="red">43.47</td>
                            <td class="red">2 - 5 Days</td>
                          </tr>
                          <tr>
                            <td class="red">2</td>                           
                            <td class="red">640</td>
                            <td class="red">43.47</td>
                            <td class="red"> 2 - 5 Days</td>
                          </tr>
                          <tr>
                            <td class="red">3</td>                           
                            <td class="red">640</td>
                            <td class="red">43.47</td>
                            <td class="red"> 2 - 5 Days</td>
                          </tr>
                          <tr>
                            <td class="red">4</td>                           
                            <td class="red">640</td>
                            <td class="red">43.47</td>
                            <td class="red"> 2 - 5 Days</td>
                          </tr>
                          <tr>
                            <th class="red">Grand Total</th>                           
                            <th class="red">67572</th>
                            <th class="red">216.37</th>
                            <th class="red"> -</th>
                          </tr>
                         
                        </tbody>
                    </table>
                    <p><strong><em>Top contributing agencies:</em></strong></p>

                    <table class="exceptional" width="100%" style="margin-bottom:30px; text-align: left;">
                      <thead>
                       
                        <tr style="font-weight: bolder;">
                          <th>Agency Name</th>                                                   
                          <th>May'20</th>                                                   
                          <th>Jun'20</th>                                                   
                          <th>Jul'20</th>
                          <th>Grand Total</th>
                          <th>Contribution %</th>                    
                      </tr>
                      </thead>
                      <tbody>
                          
                        <tr>
                          <td class="red">S S ASSOCIATES</td>
                          <td class="red">27 </td>
                          <td class="red">3119</td>
                          <td class="red">54</td>
                          <td class="red">3200 </td>
                          <td class="red">12%</td>        
                        </tr>
                        <tr>
                          <td class="red">IMPACT SERVICES</td>
                          <td class="red">27 </td>
                          <td class="red">3119</td>
                          <td class="red">54</td>
                          <td class="red">3200 </td>
                          <td class="red">12%</td>        
                        </tr>
                        <tr>
                          <td class="red">ROYAL SERVICES 3201370</td>
                          <td class="red">27 </td>
                          <td class="red">3119</td>
                          <td class="red">54</td>
                          <td class="red">3200 </td>
                          <td class="red">12%</td>        
                        </tr>
                        <tr>
                          <td class="red">SRI GIRI ASSOCIATES</td>
                          <td class="red">27 </td>
                          <td class="red">3119</td>
                          <td class="red">54</td>
                          <td class="red">3200 </td>
                          <td class="red">12%</td>        
                        </tr>
                        <tr>
                          <td class="red">VISESH CREDIT SERVICES</td>
                          <td class="red">27 </td>
                          <td class="red">3119</td>
                          <td class="red">54</td>
                          <td class="red">3200 </td>
                          <td class="red">12%</td>        
                        </tr> 
                                            
                      </tbody>
                  </table>
                  <ul>
                    <li>Major trail gap contributed by S S ASSOCIATES & Impact Services i.e. 12% of overall trail gap at Bangalore</li>
                  </ul>

                    <h3 style="font-weight: normal; color: #1d54c3; margin-top: 40px;">1.2.7. Account to Collector Ratio</h3>
                        <table class="exceptional" border="1" width="100%" style="margin-bottom: 30px; text-align: left;">
                            <thead>
                                <tr>
                                    <th colspan="10">Table 6: Account to Collector Ratio (ACR)-FOS Wise</th>
                                </tr>
                                
                            </thead>
                            <tbody>
                              <tr style="font-weight: bolder;">
                                <th></th>
                                <th colspan="2">May-20</th>
                                <th colspan="2">Jun-20 </th>
                                <th colspan="2">July-20 </th>  
                              </tr>
                              <tr>                  
                                <th class="red"> Product</th>
                                <th class="red">Maintained</th>
                                <th class="red">Crossed</th>
                                <th class="red">Maintained</th>
                                <th class="red">Crossed</th>
                                <th class="red">Maintained</th>
                                <th class="red">Crossed</th>                           
                              </tr>
                            
                              <tr>                   
                                <th class="red">BL </th>
                                <td class="red">11</td>
                                <td class="red">0</td>                      
                                <td> 13 </td>
                                <td> 0</td>    
                                <td class="red">13</td>                      
                                <td> 0</td>
                              </tr>
                              <tr>                   
                                <th class="red">CL </th>
                                <td class="red">11</td>
                                <td class="red">0</td>                      
                                <td> 13 </td>
                                <td> 0</td>    
                                <td class="red">13</td>                      
                                <td> 0</td>
                              </tr>
                              <tr>                   
                                <th class="red">MBL </th>
                                <td class="red">11</td>
                                <td class="red">0</td>                      
                                <td> 13 </td>
                                <td> 0</td>    
                                <td class="red">13</td>                      
                                <td> 0</td>
                              </tr>
                              <tr>                   
                                <th class="red">MORT </th>
                                <td class="red">11</td>
                                <td class="red">0</td>                      
                                <td> 13 </td>
                                <td> 0</td>    
                                <td class="red">13</td>                      
                                <td> 0</td>
                              </tr>
                              <tr>                   
                                <th class="red">PL </th>
                                <td class="red">11</td>
                                <td class="red">0</td>                      
                                <td> 13 </td>
                                <td> 0</td>    
                                <td class="red">13</td>                      
                                <td> 0</td>
                              </tr>
                            
                           
                            
                           
                            </tbody>
                        </table>
                        
                        
                        <ul>
                          <li>16 FOS from CSR VENTURE has crossed ACR in May’20 [Product -CL , Regular Bucket]</li>
                          <li>7 FOS from FELIDAE INFOSEC PVT LTD has crossed ACR in May’20[Product-CL, Regular Bucket]</li>
                          <li>3 FOS from MADHU TELECOM SERVICES has crossed ACR in May’20 (Product-CL, Regular Bucket]</li>
                          <li>7 FOS from OM ASSOCIATES has crossed ACR in May’20 [Product-CL, Regular Bucket]</li>
                        </ul>
                        <h3 style="color: brown;  margin-bottom: 40px;">#Data Source : DAC File</h3>



                        <p style="margin-top:40px ;"><strong>[Regular Bucket-(0-7 Bucket)]</strong></p>
                  
                        <table class="exceptional" border="1" width="100%" style="margin-bottom: 30px; text-align: left;">
                          <thead>
                            <tr>
                              <th>Agency Name</th>
                              <th colspan="2">May</th>
                              <th colspan="2">Jun</th>
                              <th colspan="2">Jul</th>
                              <th colspan="4">Avg.</th>
                            </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <th></th>
                                <th>Allocation</th>
                                <th>FOS</th>
                                <th>Allocation</th>
                                <th>FOS</th>
                                <th>Allocation</th>
                                <th>FOS</th>
                                <th>Allocation</th>
                                <th>FOS</th>
                                <th>Capacity</th>
                                <th>Gap</th>
                              </tr>
                              <tr>
                                <td>J J ASSOCIATES</td>
                                <td>2384</td>
                                <td>54</td>
                                <td>4571</td>
                                <td>50</td>
                                <td>6331</td>
                                <td>65</td>
                                <td>4429</td>
                                <td>56</td>
                                <td>4507</td>
                                <td>-78</td>                               
                              </tr>
                              <tr>
                                <td>J J ASSOCIATES</td>
                                <td>2384</td>
                                <td>54</td>
                                <td>4571</td>
                                <td>50</td>
                                <td>6331</td>
                                <td>65</td>
                                <td>4429</td>
                                <td>56</td>
                                <td>4507</td>
                                <td>-78</td>                               
                              </tr>
                              <tr>
                                <td>J J ASSOCIATES</td>
                                <td>2384</td>
                                <td>54</td>
                                <td>4571</td>
                                <td>50</td>
                                <td>6331</td>
                                <td>65</td>
                                <td>4429</td>
                                <td>56</td>
                                <td>4507</td>
                                <td>-78</td>                               
                              </tr>
                              <tr>
                                <td>J J ASSOCIATES</td>
                                <td>2384</td>
                                <td>54</td>
                                <td>4571</td>
                                <td>50</td>
                                <td>6331</td>
                                <td>65</td>
                                <td>4429</td>
                                <td>56</td>
                                <td>4507</td>
                                <td>-78</td>                               
                              </tr>
                              </tbody>
                        </table>


      
                        <ul>
                          <li>M P ASSOCIATES on an average is allocated less cases (-636) in line with the available manpower</li>
                          <li>KRISHNA ASSOCIATES on an average is allocated less cases (-390) in line with the available manpower</li>
                        </ul>
                        <h3 style="color: blue;">1.2.8. Other Observation</h3>


                        <ol>
                          <li>Endorsement cards expired of FOS</li>             
                          <li>DRA not available for FOS</li>             
                          <li>PVR not available for FOS</li>                    
                        </ol>
                        <h3 style="color: blue;">2. Parameter wise scores - Wave-wise comparison</h3>
                        <h4 style="color: brown;">Branch & Collection Agency Score Card by Product– Wave IV Vs. VII:</h4>

                        <h4 style="color: brown; margin-top: 40px;">Karnataka Snapshot</h4>
                        <table class="exceptional" border="1" width="100%" style="margin-bottom: 100px; text-align: left;">
                          <thead>
                            <tr>
                              <th style="text-align: left;">Location</th>
                              <th style="text-align: left;">Stake Holders</th>
                              <th style="text-align: left;">Audit Dates</th>
                              <th style="text-align: left;">Audits Performed</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td>
                                <ul style="text-align: left;">
                                  <li>Bangalore</li>
                                </ul>
                              </td>
                              <td>
                                <ul style="text-align: left;">
                                  <li>Branch office</li>
                                  <li>Collection Agency</li>
                                  <li>Yard Agency</li>
                                  <li>Repo Agency</li>
                                </ul>
                              </td>
                              <td>
                                <ul style="text-align: left;">
                                  <li>10th Feb to 19th                                    Feb</li>
                                
                                </ul>
                              </td>
                              <td>
                                <ul style="text-align: left;">
                                  <li>2 Branch</li>
                                  <li>8 Agencies</li>
                                  <li>59 Audits</li>
                                </ul>
                              </td>
                            </tr>
                          </tbody>
                        </table>

                        <table style="width: 100%; color: darkgoldenrod; text-align:left; font-weight:  500;">
                            <tbody>
                                <tr>
                                    <td>Private & Confidential</td>
                                    <td>Karnataka Bangalore Audit Report–Wave–VII- Q3_OCT [FY 2020-21]</td>
                                    <td>4</td>
                                </tr>
                            </tbody>
                        </table>



                    </div>
                </div>                </div>
            </div>

         
            
           

            

            

     


            

        </div>
        </div>










        <div class="main-wrap">
            
            <!-- Header -->
            
        

            <hr style="margin:0; border: 0; border-bottom: 1px solid #cccd;"/>         
            <!-- Header -->
            <div class="" id="main" style="display:block; ">            
                
                <div class="right" style="padding: 20px;     background: #cccd;                  ">                    
                   <div style="padding-left: 15px; background: #fff;"> <div style="background: #fff; padding: 15px; border-left: 20px solid brown;">            
                    <div class="" style="position: relative;padding-bottom:45px;margin-bottom: 28px; background:#fff; margin-bottom: 50px;">
                        
                      <div class="" style="margin-bottom: 15px;">
                        <img src="logo.jpg" class="" alt="" style="height:  50px;"/>
                        <img src="logo-qdegrees.png" class="" alt="" style="height:  50px; float: right;"/>
                      </div>

                     
                        <table class="exceptional" width="100%" style="text-align: left;">
                            <thead>
                              <tr>
                                <th colspan="3">  </th>         
                                <th colspan="2">Branch</th>         
                                <th colspan="2">Collection Agency</th>                                                   
                              </tr>
                              <tr style="font-weight: bolder;">
                                <td></td>                                                   
                                <td></td>                                                     
                                <td>Product</td>                                                   
                                <td>Wave-III </td>             
                                <td>Wave-IV</td>                                                     
                                <td>Wave-III </td>             
                                <td>Wave-IV</td> 
                                                                      
                            </tr>
                            </thead>
                            <tbody>
                                
          
                              <tr>
                                <td class="red">01</td>
                                <td class="red"><img src="scooter.png" style="height: 40px;" class="" alt=""/></td>
                                <td class="red">Two Wheeler</td>
                                <td class="red">96.9%</td>
                                <td class="red"> 69.1%</td>
                                <td class="red">77.5%</td>
                                <td class="red">80.7% </td>
                              </tr>

                              <tr>
                                <td class="red">02</td>
                                <td class="red"><img src="building.png" style="height: 40px;" class="" alt=""/></td>
                                <td class="red">Mort_HFC Loan</td>
                                <td class="red">96.9%</td>
                                <td class="red"> 69.1%</td>
                                <td class="red">77.5%</td>
                                <td class="red">80.7% </td>
                              </tr>
                              
                              <tr>
                                <td class="red">03</td>
                                <td class="red"><img src="brifcase.png" style="height: 40px;" class="" alt=""/></td>
                                <td class="red">Micro Business Loan</td>
                                <td class="red">96.9%</td>
                                <td class="red"> 69.1%</td>
                                <td class="red">77.5%</td>
                                <td class="red">80.7% </td>
                              </tr>
                              
                              <tr>
                                <td class="red">04</td>
                                <td class="red"><img src="laptop.png" style="height: 40px;" class="" alt=""/></td>
                                <td class="red">Consumer Loan</td>
                                <td class="red">96.9%</td>
                                <td class="red"> 69.1%</td>
                                <td class="red">77.5%</td>
                                <td class="red">80.7% </td>
                              </tr>
                              
                              <tr>
                                <td class="red">05</td>
                                <td class="red"><img src="car.png" style="height: 40px;" class="" alt=""/></td>
                                <td class="red">Auto/Used Car</td>
                                <td class="red">96.9%</td>
                                <td class="red"> 69.1%</td>
                                <td class="red">77.5%</td>
                                <td class="red">80.7% </td>
                              </tr>
                              <tr>
                                <td class="red">06</td>
                                <td class="red"><img src="doller.png" style="height: 40px;" class="" alt=""/></td>
                                <td class="red">Personal Loan</td>
                                <td class="red">96.9%</td>
                                <td class="red"> 69.1%</td>
                                <td class="red">77.5%</td>
                                <td class="red">80.7% </td>
                              </tr>
                              <tr>
                                <td class="red">07</td>
                                <td class="red"><img src="doller.png" style="height: 40px;" class="" alt=""/></td>
                                <td class="red">Business Loan</td>
                                <td class="red">96.9%</td>
                                <td class="red"> 69.1%</td>
                                <td class="red">77.5%</td>
                                <td class="red">80.7% </td>
                              </tr>
                            
                            </tbody>
                        </table>
                        <h3 style="color: blue;">3. Branch Journey</h3>

                        <table class="exceptional" width="100%" style="text-align: left; padding-top: 30px;">
                          <thead>
                            <tr>
                 
                              <th colspan="4">Repo Process</th>                                                   
                            </tr>
                            <tr style="font-weight: bolder;">
                              <td></td>                                                   
                              <td></td>                                                     
                                                                              
                              <td>Wave-III </td>             
                              <td>Wave -IV</td> 
                                                                    
                          </tr>
                          </thead>
                          <tbody>
                              
        
                            <tr>
                              <td class="red">01</td>
                             
                              <td class="red">Repo Agency</td>
                              <td class="red">96.9%</td>
                              <td class="red">96.9%</td>
                      
                            </tr>

                            <tr>
                              <td class="red">02</td>
                             
                              <td class="red">Repo Branch</td>
                              <td class="red">96.9%</td>
                              <td class="red">96.9%</td>
                           
                            </tr>
                            
                            <tr>
                              <td class="red">03</td>
                             
                              <td class="red">Yard</td>
                              <td class="red">96.9%</td>
                              <td class="red">96.9%</td>
                
                            </tr>
                            

                          </tbody>
                      </table>


                      <h3 style="color: brown; margin-top: 40px;">                        Branch Journey by Product:</h3>
                      
                      <table class="new-table" border="1" width="100%" style="text-align: left;">
                        <thead>
                          <tr>
                            <th colspan="6">Branch Journey by Product:</th>
                          </tr>    
                        </thead>
                        <tbody style="text-align: left;">
                          <tr>
                            <td rowspan="3"><img src="brifcase.png" style="height: 30px;"/></td>
                            <th>50%</th>
                            <th >NA</th>
                            <th >100%</th>
                            <th>NA </th>
                            <th >44%</th>
                          </tr> 
                          <tr>
                           
                            <th style="">Allocation</th>
                            <th style="">Cash Management</th>
                            <th style="">Branch Back Office</th>
                            <th style="">In-house team – Cash Management </th>
                            <th style="">Agency Management</th>
                          </tr>
                          <tr>
                  
                            <td>
                              <ul style="text-align: left;">
                                <li>Cheque/DD Summary not maintained</li>
                                <li>Inventory register not maintained</li>
                              </ul>
                            </td>
                            <td>
                              <ul style="text-align: left;">
                                <li>Cheque/DD Summary not maintained</li>
                                <li>Inventory register not maintained</li>
                              </ul>
                            </td>
                            <td>
                              <ul style="text-align: left;">
                                <li>Cheque/DD Summary not maintained</li>
                                <li>Inventory register not maintained</li>
                              </ul>
                            </td>
                            <td>
                              <ul style="text-align: left;">
                                <li>Cheque/DD Summary not maintained</li>
                                <li>Inventory register not maintained</li>
                              </ul>
                            </td>
                            <td>
                              <ul style="text-align: left;">
                                <li>Cheque/DD Summary not maintained</li>
                                <li>Inventory register not maintained</li>
                              </ul>
                            </td>
                          
                          </tr>
                         
                        </tbody>
                      </table>



                     
                      
                      <table class="new-table" border="1" width="100%" style="margin-top: 30px; text-align: left;">
                        <thead>
                          <tr>
                            <th colspan="6">Overall Agency Journey (Includes all products)</th>
                          </tr>    
                        </thead>
                        <tbody style="text-align: left;">
                          <tr>
                            
                            <th>75%</th>
                            <th >93%</th>
                            <th >100%</th>
                            <th>71%</th>
                            <th >89%</th>
                            <th>100%</th>
                          </tr> 
                          <tr>
                           
                            <th style="">Allocation</th>
                            <th style="">Cash Management</th>
                            <th style="">Information Security</th>
                            <th style="">Agency Back Office </th>
                            <th style="">FOS Analysis</th>
                            <th style="">Settlement Analysis</th>
                          </tr>
                          <tr>
                  
                            <td>
                              <ul style="text-align: left;">
                                <li>Cheque/DD Summary not maintained</li>
                                <li>Inventory register not maintained</li>
                              </ul>
                            </td>
                            <td>
                              <ul style="text-align: left;">
                                <li>Cheque/DD Summary not maintained</li>
                                <li>Inventory register not maintained</li>
                              </ul>
                            </td>
                            <td>
                              <ul style="text-align: left;">
                                <li>Cheque/DD Summary not maintained</li>
                                <li>Inventory register not maintained</li>
                              </ul>
                            </td>
                            <td>
                              <ul style="text-align: left;">
                                <li>Cheque/DD Summary not maintained</li>
                                <li>Inventory register not maintained</li>
                              </ul>
                            </td>
                            <td>
                              <ul style="text-align: left;">
                                <li>Cheque/DD Summary not maintained</li>
                                <li>Inventory register not maintained</li>
                              </ul>
                            </td>
                            <td>
                              <ul style="text-align: left;">
                                <li>Cheque/DD Summary not maintained</li>
                                <li>Inventory register not maintained</li>
                              </ul>
                            </td>
                          
                          </tr>
                         
                        </tbody>
                      </table>
                      
             










                        <h3 style="color: brown; margin-bottom: 40px;">#Data Source : DAC File</h3>
                        
                        <!--<h3 style="color: brown;">Red Alert Summary:</h3>
                        
                        <table class="etable" border="1" width="100%" style="margin-top: 30px; text-align: left;">
                          <thead>
                            <tr>
                              <th colspan="2" style="background: orange;">                  Red Alert Shared            </th>
                              <th colspan="2"  style="background: brown;">                 Feed Back Received             </th>
                              <th colspan="2" style="background: grey;">            Pending                  </th>
                            </tr>                            
                          </thead>
                          <tbody>
                            <tr>
                              <th colspan="2">                    8          </th>
                              <th colspan="2">                     5         </th>
                              <th colspan="2">                      3        </th>
                            </tr>
                          </tbody>
                        </table>
                        <P>Total 8 red alerts (18 Deviations) shared for Jharkhand state – Fair Advisory Legal Services, Jai Sai Services, Branch Cash
                          Audit ,M/S Rajkishor Yadav Yard and Branch Repo</P>
                        <table class="exceptional"  width="100%" style="margin-bottom: 100px; text-align: left; border: 1px solid #ccc;">
                            <thead>
                               
                              <tr style="font-weight: bolder;">
                                <th>Product Name</th>
                                <th>Type</th>
                                <th>Issue </th>
                                <th>Action Plan Received</th>
                                                        
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td class="red">TW</td>
                                <td class="red" > Branch Repo-
                                  Jamshedpur</td>
                                <td class="red" style="text-align: left;">
                                  <ul>
                                    <li>Pre and Post Sale Notice</li>
                                    <li>Incomplete Repo Kit/Inventory
                                      sheet</li>
                                    <li>Pre and Post Repossession
                                      Letter</li>
                                   
                                  </ul>  
                                </td>
                                <td style="text-align: left;">
                                  <ul>
                                    <li>There was discrepancy in this same. By
                                      mistakenly he has forgotten to
                                      maintained all the process and this type
                                      of mistake never happen in future</li>
                                  </ul>
                                </td>
                                
                              


                              </tr>
                            
                              <tr>
                   
                                <td class="red">TW </td>
                                <td class="red">Branch Repo-
                                  Ranchi</td>
                             
                                  <td class="red" style="text-align: left;">
                                    <ul>
                                      <li>Pre and Post Repossession
                                        Letter</li>
                                      <li>Pre and Post Sale Notice</li>
                                      <li>Incomplete Repo Kit/Inventory                                        sheet</li>
                                     
                                    </ul> 
                                </td>                      
                                <td style="text-align: left;">
                                  <ul>
                                    <li>There was discrepancy in this same. By
                                      mistakenly he has forgotten to
                                      maintained all the process and this type
                                      of mistake never happen in future.</li>
                                  </ul>
                                </td>
                      


                              </tr>
                            
                              <tr>
                                <td class="red" >Cash Audit</td>
                                <td class="red" > Branch</td>
                        
                                  <td class="red" style="text-align: left; width: 300px;">
                                    <ul>
                                      <li>Cash Vault not covering by
                                        CCTV</li>
                                     
                                    </ul> 
                                </td>
                                <td style="text-align: left;">
                                  <ul>
                                    <li>I have crosschecked the same and there
                                      was discrepancy in this same. By
                                      mistakenly he has forgotten to
                                      maintained the process and this type of
                                      mistake never happen in future</li>
                                  </ul>
                                </td>
             

                              </tr>
                            
                              <tr>
                                <td class="red">TW</td>
                                <td class="red" >Collection
                                  Agency</td>
                       
                                  <td class="red" style="text-align: left;">
                                    <ul>
                                      <li>Delay in Secondary allocation</li>
                                      <li>Account to Collector Ratio (ACR)</li>                                                                           
                                    </ul> 
                                </td>
                                <td style="text-align: left;">
                                  <ul>
                                    <li>There was discrepancy in this same. By
                                      mistakenly he has forgotten to
                                      maintained all the process and this type
                                      of mistake never happen in future</li>
                                  </ul>
                                </td>
                                
                              


                              </tr>
                            
                              <tr>
                   
                                <td class="red">TW </td>
                                <td class="red">Yard Agency</td>
                      
                                  <td class="red" style="text-align: left;">
                                    <ul>
                                      <li>Incomplete Repo Kit/Inventory                                        Sheet</li>
                                      <li>CCTV Issue</li>
                                      <li>Vehicle not parked in yard</li>
                                     
                                    </ul> 
                                </td>                      
                                <td style="text-align: left;">
                                  <ul>
                                    <li>There was discrepancy in this same. By
                                      mistakenly he has forgotten to
                                      maintained all the process and this type
                                      of mistake never happen in future</li>
                                  </ul>
                                </td>
                      


                              </tr>
                            
                            </tbody>
                        </table>-->


                        <h3 style="color: blue;">4. Scoring Methodology</h3>
                        

                        <table class="etable"  width="100%" border="1" style="margin-bottom: 100px; text-align: center; border: 1px solid #ccc;">
                          <thead>
                            <tr>
                              <th style="background: #d32600;">Agency</th>
                              <td colspan="2"></td>
                              <th style="background: skyblue;">Yard/Repo Agency</th>
                            </tr>
                          </thead>  
                          <tbody>
                            <tr>
                              <td>60%</td>
                              <td colspan="2">Collection Manager - TW/UC</td>
                              <td>40%</td>
                            </tr>
                            <tr>
                              <td>100%</td>
                              <td colspan="2">Collection Manager - Other Products</td>
                              <td>-</td>
                            </tr>
                          </tbody>
                        </table>

                        <table class="etable"  width="100%" border="1" style="margin-bottom: 100px; text-align: center; border: 1px solid #ccc;">
                          <thead>
                            <tr>
                              <th style="background: #d32600;">Agency</th>
                              <td colspan="2"></td>
                              <th style="background: #ffae94;">Yard/Repo Agency</th>
                            </tr>
                          </thead> 
                          <tbody>
                            <tr>
                              <td>70%</td>
                              <td colspan="2">Area Collection Manager</td>
                              <td>30%</td>
                            </tr>
                           
                          </tbody>
                        </table>

                        <table class="etable"  width="100%" border="1" style="margin-bottom: 100px; text-align: center; border: 1px solid #ccc;">
                          <thead>
                            <tr>
                              <th style="background: #d32600;">Agency</th>
                              <td colspan="2"></td>
                              <th style="background: #ffae94">Yard/Repo Agency</th>
                            </tr>
                          </thead>  
                          <tbody>
                            <tr>
                              <td>70%</td>
                              <td colspan="2">Regional Collection Manager</td>
                              <td>30%</td>
                            </tr>
                      
                          </tbody>
                        </table>
                        <table class="etable"  width="100%" border="1" style="margin-bottom: 100px; text-align: center; border: 1px solid #ccc;">
                          <thead>
                            <tr>
                              <th style="background: #d32600;">Agency</th>
                              <td colspan="2"></td>
                              <th style="background: #ffae94">Yard/Repo Agency</th>
                            </tr>
                          </thead>  
                          <tbody>
                            <tr>
                              <td>80%</td>
                              <td colspan="2">Zonal Collection Manager</td>
                              <td>20%</td>
                            </tr>
                           
                          </tbody>
                        </table>
                        <table class="etable"  width="100%" border="1" style="margin-bottom: 100px; text-align: center; border: 1px solid #ccc;">
                          <thead>
                            <tr>
                              <th style="background: #d32600;">Agency</th>
                              <td colspan="2"></td>
                              <th style="background: #ffae94">Yard/Repo Agency</th>
                            </tr>
                          </thead>  
                          <tbody>
                            <tr>
                              <td>80%</td>
                              <td colspan="2">National Collection Manager</td>
                              <td>20%</td>
                            </tr>
                       
                          </tbody>
                        </table>





                        <table style="width: 100%; color: darkgoldenrod; text-align:left; font-weight:  500;">
                            <tbody>
                                <tr>
                                    <td>Private & Confidential</td>
                                    <td>Karnataka Bangalore Audit Report–Wave–VII- Q3_OCT [FY 2020-21]</td>
                                    <td>4</td>
                                </tr>
                            </tbody>
                        </table>



                    </div>
                </div>                </div>
            </div>

         
            
           

            

            

     


            

        </div>
        </div>




        












        <div class="main-wrap">
          
          
          <hr style="margin:0; border: 0; border-bottom: 1px solid #cccd;"/>         
          <!-- Header -->
          <div class="" id="main" style="display: block;">            
              
              <div class="right" style="padding: 20px;     background: #cccd;                     ">                    
                 <div style="padding-left: 15px; background: #fff;"> 
                      <div style="background: #fff; padding: 15px; border-left: 20px solid brown;">
                        <div class="" style="margin-bottom: 15px;">
                          <img src="logo.jpg" class="" alt="" style="height:  50px;"/>
                          <img src="logo-qdegrees.png" class="" alt="" style="height:  50px; float: right;"/>
                        </div>
                        <h3 style="color: #1d54c3;">5. Audit Findings – Bangalore </h3>
                        <h1  style="font-weight:bold; font-size: 54px; margin: 250px auto; color: #1d54c3; text-align: center;">Product & Cash Management
                          <br/>–<br/>
                          Detailed Report</h1>

                          


                        <table style="width: 100%; color: darkgoldenrod; text-align:left; font-weight:  500;">
                          <tbody>
                              <tr>
                                  <td>Private & Confidential</td>
                                  <td>Karnataka Bangalore Audit Report–Wave–VII- Q3_OCT [FY 2020-21]</td>
                                  <td>4</td>
                              </tr>
                          </tbody>
                      </table>
                      </div>
                  </div>                
              </div>
          </div>

      </div>





      <div class="main-wrap">
          
    
        <hr style="margin:0; border: 0; border-bottom: 1px solid #cccd;"/>         
        <!-- Header -->
        <div class="" id="main" style="display: block;">            
            
            <div class="right" style="padding: 20px;     background: #cccd;            ">                    
               <div style="padding-left: 15px; background: #fff;"> 
                    <div style="background: #fff; padding: 15px; border-left: 20px solid brown;">
                      <div class="" style="margin-bottom: 15px;">
                        <img src="logo.jpg" class="" alt="" style="height:  50px;"/>
                        <img src="logo-qdegrees.png" class="" alt="" style="height:  50px; float: right;"/>
                      </div>
                      <h3 style="color: brown; ">5.1. Bangalore Branch – Cash Management Audit</h3>

                    
                      <h3 style="font-weight: normal; color: #1d54c3; ">Cash Management Process</h3>
                      <ul style="margin-bottom: 40px;">
                        <li>Cash Vault not captured by CCTV (Only for Observation)</li>
                        <li>Inventory register not available at Branch</li>
                        <li>Cheque/DD Summary not maintained by Branch</li>
                       
                      </ul>  
                      <h3 style="color: brown; ">5.2. Branch Bangalore – Product Wise:</h3>
                      <p>This section provides detail deviations found in the Jaipur branch during audit process</p>
                      <h3 style="font-weight: normal; color: #1d54c3; ">Auto-Used Car Loan:</h3>
                      <ul style="margin-bottom: 40px;">
                        <li>Agency performance report not shared by Collection Manager</li>
                        <li>PTP Date not followed as per PTP date Sample Loan Number: 19007709 & 14610016 (Only for Observation)</li>
                        <li>Pre and Post Repossession
                          Letter</li>
                       
                      </ul>  
                      
                      <h3 style="font-weight: normal; color: #1d54c3; ">Business Loan:</h3>
                      <ul style="margin-bottom: 40px;">
                        <li>Agency performance report not shared by Collection Manager</li>
                        <li>PTP Date not followed as per PTP date Sample Loan Number: 19007709 & 14610016 (Only for Observation)</li>
                        <li>Pre and Post Repossession
                          Letter</li>
                       
                      </ul>  


                      <h3 style="font-weight: normal; color: #1d54c3; ">Consumer Loan:</h3>
                      <ul style="margin-bottom: 40px;">
                        <li>Agency performance report not shared by Collection Manager</li>
                        <li>PTP Date not followed as per PTP date Sample Loan Number: 19007709 & 14610016 (Only for Observation)</li>
                        <li>Pre and Post Repossession
                          Letter</li>
                       
                      </ul>  


                      <h3 style="font-weight: normal; color: #1d54c3; ">Micro Business Loan:</h3>
                      <ul style="margin-bottom: 40px;">
                        <li>Agency performance report not shared by Collection Manager</li>
                        <li>PTP Date not followed as per PTP date Sample Loan Number: 19007709 & 14610016 (Only for Observation)</li>
                        <li>Pre and Post Repossession
                          Letter</li>
                       
                      </ul> 
                      
                      
                      
                      <h3 style="font-weight: normal; color: #1d54c3; ">Personal Loan:</h3>
                      <ul style="margin-bottom: 40px;">
                        <li>Agency performance report not shared by Collection Manager</li>
                        <li>PTP Date not followed as per PTP date Sample Loan Number: 19007709 & 14610016 (Only for Observation)</li>
                        <li>Pre and Post Repossession Letter</li>
                      </ul> 

                      
                      <h3 style="font-weight: normal; color: #1d54c3; ">Two-Wheeler Loan:</h3>
                      <ul style="margin-bottom: 40px;">
                        <li>Agency performance report not shared by Collection Manager</li>
                        <li>PTP Date not followed as per PTP date Sample Loan Number: 19007709 & 14610016 (Only for Observation)</li>
                        <li>Pre and Post Repossession
                          Letter</li>
                       
                      </ul> 

                      
                      
                      <h3 style="color: brown; ">5.3. Branch Repo – Product Wise:</h3>
                      <h3 style="font-weight: normal; color: #1d54c3; ">Two-Wheeler Loan:</h3>
                      <ul style="margin-bottom: 40px;">
                        <li>Agency performance report not shared by Collection Manager</li>
                        <li>PTP Date not followed as per PTP date Sample Loan Number: 19007709 & 14610016 (Only for Observation)</li>
                        <li>Pre and Post Repossession
                          Letter</li>
                       
                      </ul>  

                      <h3 style="font-weight: normal; color: #1d54c3; ">Auto-Used Car Loan:</h3>
                      <ul style="margin-bottom: 40px;">
                        <li>Agency performance report not shared by Collection Manager</li>
                        <li>PTP Date not followed as per PTP date Sample Loan Number: 19007709 & 14610016 (Only for Observation)</li>
                        <li>Pre and Post Repossession
                          Letter</li>
                       
                      </ul>  
                      

                      <h3 style="color: brown; ">5.4. Bangalore Collection Agency – Product Wise:</h3>
                      <h4 style="color: brown; ">5.4.1. Agency A S Associates</h4>
                      <table class="exceptional" width="100%" style="margin-bottom: 100px; text-align: left; border: 1px solid #ccc;">
                        <thead>
                            <tr>
                                <th colspan="10">Table 7 : Agency A S Associates - Product-Wise – Key Issues</th>
                            </tr>
                          <tr style="font-weight: bolder;">
                            <td style="width: 200px;">Product Name</td>
                     
                            <td>Remarks</td>
                                                    
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                          
                            <th class="red"> Business Loan</th>
                     
                            <td style="text-align: left;">
                              <ul>
                                <li><strong>Agency Back Office:</strong><br/>
                                  
                                  <ul style="margin: 10px auto;">                                  
                                    <li>DRA certificate not available for 1 FOS</li>
                                    <li>PVR not available for 1 FOS</li>
                                  </li>
                                </li>
                              </ul>
                            </td>
                            
                          


                          </tr>
                          <tr>
                          
                            <th class="red"> Two-Wheeler Loan</th>
                     
                            <td style="text-align: left;">
                              <ul>
                                <li><strong>Allocation:</strong><br/>
                                  
                                  <ul style="margin: 10px auto;">                                  
                                    <li>Delay in secondary allocation of 605 case, POS Rs. 2.32 Cr</li>
                                    <li>Trail Gap for 11 cases, POS value Rs. 5.13 Lac</li>
                                    <li>Trail Intensity is very low and less trail attempt on the assigned cases by FOS and found 136 cases are less than attempt 5</li>
                                    <li>ACR not maintained by all 04 FOS in last three months.</li>
                                  </ul>
                                </li>

                                <li><strong>Agency Back Office’s</strong><br/>
                                  
                                  <ul style="margin: 10px auto;">                                  
                                    
                                    <li>PVR not available for 1 FOS</li>
                                    <li>DRA certificate not available for 1 FOS</li>

                                  </ul>
                                </li>
                              </ul>
                            </td>
                            
                          


                          </tr>
                          <tr>
                          
                            <th class="red"> Consumer Loan</th>
                     
                            <td style="text-align: left;">
                              <ul>
                                <li><strong>Allocation:</strong><br/>
                                  
                                  <ul style="margin: 10px auto;">                                  
                                    <li>Delay in secondary allocation of 605 case, POS Rs. 2.32 Cr</li>
                                    <li>Trail Gap for 11 cases, POS value Rs. 5.13 Lac</li>
                                    <li>Trail Intensity is very low and less trail attempt on the assigned cases by FOS and found 136 cases are less than attempt 5</li>
                                    <li>ACR not maintained by all 04 FOS in last three months.</li>
                                  </ul>
                                </li>

                                <li><strong>Agency Back Office’s</strong><br/>
                                  
                                  <ul style="margin: 10px auto;">                                  
                                    
                                    <li>PVR not available for 1 FOS</li>
                                    <li>DRA certificate not available for 1 FOS</li>

                                  </ul>
                                </li>
                              </ul>
                            </td>
                            
                          


                          </tr>
                          <tr>
                          
                            <th class="red"> Personal Loan</th>
                     
                            <td style="text-align: left;">
                              <ul>
                                <li><strong>Allocation:</strong><br/>
                                  
                                  <ul style="margin: 10px auto;">                                  
                                    <li>Delay in secondary allocation of 605 case, POS Rs. 2.32 Cr</li>
                                    <li>Trail Gap for 11 cases, POS value Rs. 5.13 Lac</li>
                                    <li>Trail Intensity is very low and less trail attempt on the assigned cases by FOS and found 136 cases are less than attempt 5</li>
                                    <li>ACR not maintained by all 04 FOS in last three months.</li>
                                  </ul>
                                </li>

                                <li><strong>Agency Back Office’s</strong><br/>
                                  
                                  <ul style="margin: 10px auto;">                                  
                                    
                                    <li>PVR not available for 1 FOS</li>
                                    <li>DRA certificate not available for 1 FOS</li>

                                  </ul>
                                </li>
                              </ul>
                            </td>
                            
                          


                          </tr>
                                              
                        
                        </tbody>
                    </table>



                    <h4 style="color: brown; ">5.4.2. Agency Ace Management Solutions</h4>
                    <table class="exceptional" width="100%" style="margin-bottom: 100px; text-align: left; border: 1px solid #ccc;">
                      <thead>
                          <tr>
                              <th colspan="10">Table 8 : Fair Advisory Legal Services - Product-Wise – Key Issues</th>
                          </tr>
                        <tr style="font-weight: bolder;">
                          <td style="width: 200px;">Product Name</td>
                   
                          <td>Remarks</td>
                                                  
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                        
                          <th class="red"> Business Loan</th>
                   
                          <td style="text-align: left;">
                            <ul>
                              <li><strong>Agency Back Office:</strong><br/>
                                
                                <ul style="margin: 10px auto;">                                  
                                  <li>DRA certificate not available for 1 FOS</li>
                                  <li>PVR not available for 1 FOS</li>
                                </li>
                              </li>
                            </ul>
                          </td>
                          
                        


                        </tr>
                            
                      
                      </tbody>
                  </table>

                  <h4 style="color: brown; ">5.4.3. Agency Aeon Business Solutions</h4>
                    <table class="exceptional" width="100%" style="margin-bottom: 100px; text-align: left; border: 1px solid #ccc;">
                      <thead>
                          <tr>
                              <th colspan="10">Table 9 : Jai Sai Services - Product-Wise – Key Issues</th>
                          </tr>
                        <tr style="font-weight: bolder;">
                          <td style="width: 200px;">Product Name</td>
                   
                          <td>Remarks</td>
                                                  
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                        
                          <th class="red"> Business Loan</th>
                   
                          <td style="text-align: left;">
                            <ul>
                              <li><strong>Agency Back Office:</strong><br/>
                                
                                <ul style="margin: 10px auto;">                                  
                                  <li>DRA certificate not available for 1 FOS</li>
                                  <li>PVR not available for 1 FOS</li>
                                </li>
                              </li>
                            </ul>
                          </td>
                          
                        


                        </tr>
                        <tr>
                        
                          <th class="red"> Business Loan</th>
                   
                          <td style="text-align: left;">
                            <ul>
                              <li><strong>Agency Back Office:</strong><br/>
                                
                                <ul style="margin: 10px auto;">                                  
                                  <li>DRA certificate not available for 1 FOS</li>
                                  <li>PVR not available for 1 FOS</li>
                                </li>
                              </li>
                            </ul>
                          </td>
                          
                        


                        </tr>
                            
                        <tr>
                        
                          <th class="red"> Business Loan</th>
                   
                          <td style="text-align: left;">
                            <ul>
                              <li><strong>Agency Back Office:</strong><br/>
                                
                                <ul style="margin: 10px auto;">                                  
                                  <li>DRA certificate not available for 1 FOS</li>
                                  <li>PVR not available for 1 FOS</li>
                                </li>
                              </li>
                            </ul>
                          </td>
                          
                        


                        </tr>
                            
                            
                      
                      </tbody>
                  </table>




                  <h4 style="color: brown; ">5.4.4. Agency Akshaya Associates</h4>
                  <table class="exceptional" width="100%" style="margin-bottom: 100px; text-align: left; border: 1px solid #ccc;">
                    <thead>
                        <tr>
                            <th colspan="10">Table 10 : MITHILA SERVICES - Product-Wise – Key Issues</th>
                        </tr>
                      <tr style="font-weight: bolder;">
                        <td style="width: 200px;">Product Name</td>
                 
                        <td>Remarks</td>
                                                
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                      
                        <th class="red"> Business Loan</th>
                 
                        <td style="text-align: left;">
                          <ul>
                            <li><strong>Agency Back Office:</strong><br/>
                              
                              <ul style="margin: 10px auto;">                                  
                                <li>DRA certificate not available for 1 FOS</li>
                                <li>PVR not available for 1 FOS</li>
                              </li>
                            </li>
                          </ul>
                        </td>
                        
                      


                      </tr>
                      <tr>
                      
                        <th class="red"> Business Loan</th>
                 
                        <td style="text-align: left;">
                          <ul>
                            <li><strong>Agency Back Office:</strong><br/>
                              
                              <ul style="margin: 10px auto;">                                  
                                <li>DRA certificate not available for 1 FOS</li>
                                <li>PVR not available for 1 FOS</li>
                              </li>
                            </li>
                          </ul>
                        </td>
                        
                      


                      </tr>
                          
                      <tr>
                      
                        <th class="red"> Business Loan</th>
                 
                        <td style="text-align: left;">
                          <ul>
                            <li><strong>Agency Back Office:</strong><br/>
                              
                              <ul style="margin: 10px auto;">                                  
                                <li>DRA certificate not available for 1 FOS</li>
                                <li>PVR not available for 1 FOS</li>
                              </li>
                            </li>
                          </ul>
                        </td>
                        
                      


                      </tr>
                      <tr>
                      
                        <th class="red"> Business Loan</th>
                 
                        <td style="text-align: left;">
                          <ul>
                            <li><strong>Agency Back Office:</strong><br/>
                              
                              <ul style="margin: 10px auto;">                                  
                                <li>DRA certificate not available for 1 FOS</li>
                                <li>PVR not available for 1 FOS</li>
                              </li>
                            </li>
                          </ul>
                        </td>
                        
                      


                      </tr>
                      <tr>
                      
                        <th class="red"> Business Loan</th>
                 
                        <td style="text-align: left;">
                          <ul>
                            <li><strong>Agency Back Office:</strong><br/>
                              
                              <ul style="margin: 10px auto;">                                  
                                <li>DRA certificate not available for 1 FOS</li>
                                <li>PVR not available for 1 FOS</li>
                              </li>
                            </li>
                          </ul>
                        </td>
                        
                      


                      </tr>
                          
                      <tr>
                      
                        <th class="red"> Business Loan</th>
                 
                        <td style="text-align: left;">
                          <ul>
                            <li><strong>Agency Back Office:</strong><br/>
                              
                              <ul style="margin: 10px auto;">                                  
                                <li>DRA certificate not available for 1 FOS</li>
                                <li>PVR not available for 1 FOS</li>
                              </li>
                            </li>
                          </ul>
                        </td>
                        
                      


                      </tr>
                          
                    
                    </tbody>
                </table>



              
              <h3 style="color: brown; ">  5.5. Bangalore Repo Agency – Product Wise:</h3>
              <h4 style="color: brown; ">  5.5.1. Repo Agency Hamsa Associates</h4>
                <table class="exceptional" width="100%" style="margin-bottom: 100px; text-align: left; border: 1px solid #ccc;">
                  <thead>
                      <tr>
                          <th colspan="10">Table 13:Repo Agency Hamsa Associates - Product-Wise – Key Issues</th>
                      </tr>
                    <tr style="font-weight: bolder;">
                      <td style="width: 200px;">Product Name</td>
               
                      <td>Remarks</td>
                                              
                    </tr>
                  </thead>
                  <tbody>
                    <tr>                    
                      <th class="red"> Two-Wheeler Loan</th>               
                      <td style="text-align: left;">
                        <ul>
                          <li>                            
                            <ul style="margin: 10px auto;">                                  
                              <li>Repo Agency agreement not available at agency</li>
                            </ul>
                          </li>                        
                        </ul>                        
                      </td>
                    </tr>
                  </tbody>
              </table>
              
           
              <h4 style="color: brown; ">  5.5.2. Repo Agency J J Associates</h4>
                <table class="exceptional" width="100%" style="margin-bottom: 100px; text-align: left; border: 1px solid #ccc;">
                  <thead>
                      <tr>
                          <th colspan="10">Table 14: MS KALYANI ASSOCIATES - Product-Wise – Key Issues</th>
                      </tr>
                    <tr style="font-weight: bolder;">
                      <td style="width: 200px;">Product Name</td>
               
                      <td>Remarks</td>
                                              
                    </tr>
                  </thead>
                  <tbody>
                    <tr>                    
                      <th class="red"> Two-Wheeler Loan</th>               
                      <td style="text-align: left;">
                        <ul>
                          <li>                       
                            <ul style="margin: 10px auto;">                                
                          
                              <li>Repo Agency agreement not available at agency</li>
                              <li>Repo request not raised through mail (Only for Observation)</li>
                              <li>Vehicle parked at Yard beyond TAT time Repo Kit Number 24220 & 24221.</li>
                            </ul>
                          </li>
                        </ul>                        
                      </td>
                    </tr>
               
                  </tbody>
              </table>
              <h3 style="color: brown; "> 5.6. Bangalore Yard Agency – Product Wise:</h3>
              <h4 style="color: brown; ">5.6.1. Yard Agency Ruby Enterprises (Location:- Mylsandra)</h4>
              <table class="exceptional" width="100%" style="margin-bottom: 100px; text-align: left; border: 1px solid #ccc;">
                <thead>
                    <tr>
                        <th colspan="10">Table 15: Yard Agency Ruby Enterprises - Product-Wise – Key Issues</th>
                    </tr>
                  <tr style="font-weight: bolder;">
                    <td style="width: 200px;">Product Name</td>
             
                    <td>Remarks</td>
                                            
                  </tr>
                </thead>
                <tbody>
                  <tr>                    
                    <th class="red"> Two-Wheeler Loan</th>               
                    <td style="text-align: left;">
                      <ul>
                        <li>                       
                          <ul style="margin: 10px auto;">                                
                        
                            <li>Repo Agency agreement not available at agency</li>
                            <li>Repo request not raised through mail (Only for Observation)</li>
                            <li>Vehicle parked at Yard beyond TAT time Repo Kit Number 24220 & 24221.</li>
                          </ul>
                        </li>
                      </ul>                        
                    </td>
                  </tr>
             
                </tbody>
            </table>


                        <table style="width: 100%; color: darkgoldenrod; text-align:left; font-weight:  500;">
                            <tbody>
                                <tr>
                                    <td>Private & Confidential</td>
                                    <td>Karnataka Bangalore Audit Report–Wave–VII- Q3_OCT [FY 2020-21]</td>
                                    <td>4</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>                
            </div>
        </div>

    </div>

    
   v>

<div class="main-wrap">
          
         
  <!-- Header -->
  <div class="" id="main" style="display: block;">            
      
      <div class="right" style="padding: 20px;     background: #cccd;                 ">                    
         <div style="padding-left: 15px; background: #fff;">    
              <div style="background: #fff; padding: 15px; border-left: 20px solid brown;">
                <div class="" style="margin-bottom: 15px;">
                  <img src="logo.jpg" class="" alt="" style="height:  50px;"/>
                  <img src="logo-qdegrees.png" class="" alt="" style="height:  50px; float: right;"/>
                </div>
                <h3 style="color: brown; "> 5.4. Ranchi Collection Agency – Product Wise:</h3>
                <h4 style="color: brown; "> 5.4.1. Agency FAIR ADVISORY AND LEGALE</h4>

                <table class="exceptional" width="100%" style="margin-bottom: 100px; text-align: left; border: 1px solid #ccc;">
                  <thead>
                      <tr>
                          <th colspan="10">Table 16: FAIR ADVISORY AND LEGALE - Product-Wise – Key Issues</th>
                      </tr>
                    <tr style="font-weight: bolder;">
                      <td style="width: 200px;">Product Name</td>
               
                      <td>Remarks</td>
                                              
                    </tr>
                  </thead>
                  <tbody>
                    <tr>                    
                      <th class="red"> Business Loan</th>               
                      <td style="text-align: left;">
                        <ul>
                          <li><strong>Allocation:</strong><br>
                            
                            <ul style="margin: 10px auto;">                        
                              <li>Allocation gap of 3 cases, POS value Rs 19.26 Lac</li>          
                              <li>Trail Gap of 2 cases, POS value Rs. 1.16 Lac</li>
                              <li>Trail Intensity is very low and less trail attempt on the assigned cases by FOS and found 3 cases are less than attempt 5</li>
                            </ul>
                          </li>
                          <li><strong>Information Security:</strong><br>
                            
                            <ul style="margin: 10px auto;">                                  
                              <li>USB access available in agency system (Only for Observation)</li>
                            </ul>
                          </li>
                          <li><strong>Agency Back Office:</strong><br>
                            
                            <ul style="margin: 10px auto;">                                  
                              <li>PVR not available for 2 FOS</li>
                            </ul>
                          </li>
                        </ul>
                        
                      </td>
                    </tr>
                    <tr>                    
                      <th class="red"> Micro Business Loan</th>               
                      <td style="text-align: left;">
                        <ul>
                          <li><strong>Allocation:</strong><br>
                            
                            <ul style="margin: 10px auto;">                        
                              <li>Allocation gap of 3 cases, POS value Rs 19.26 Lac</li>          
                              <li>Trail Gap of 2 cases, POS value Rs. 1.16 Lac</li>
                              <li>Trail Intensity is very low and less trail attempt on the assigned cases by FOS and found 3 cases are less than attempt 5</li>
                            </ul>
                          </li>
                          <li><strong>Information Security:</strong><br>
                            
                            <ul style="margin: 10px auto;">                                  
                              <li>USB access available in agency system (Only for Observation)</li>
                            </ul>
                          </li>
                          <li><strong>Agency Back Office:</strong><br>
                            
                            <ul style="margin: 10px auto;">                                  
                              <li>PVR not available for 2 FOS</li>
                            </ul>
                          </li>
                        </ul>
                        
                      </td>
                    </tr>
                    <tr>                    
                      <th class="red"> Two-Wheeler Loan</th>               
                      <td style="text-align: left;">
                        <ul>
                          <li><strong>Allocation:</strong><br>
                            
                            <ul style="margin: 10px auto;">                        
                              <li>Allocation gap of 3 cases, POS value Rs 19.26 Lac</li>          
                              <li>Trail Gap of 2 cases, POS value Rs. 1.16 Lac</li>
                              <li>Trail Intensity is very low and less trail attempt on the assigned cases by FOS and found 3 cases are less than attempt 5</li>
                            </ul>
                          </li>
                          <li><strong>Information Security:</strong><br>
                            
                            <ul style="margin: 10px auto;">                                  
                              <li>USB access available in agency system (Only for Observation)</li>
                            </ul>
                          </li>
                          <li><strong>Agency Back Office:</strong><br>
                            
                            <ul style="margin: 10px auto;">                                  
                              <li>PVR not available for 2 FOS</li>
                            </ul>
                          </li>
                        </ul>
                        
                      </td>
                    </tr>
                    <tr>                    
                      <th class="red">Consumer Loan</th>               
                      <td style="text-align: left;">
                        <ul>
                          <li><strong>Allocation:</strong><br>
                            
                            <ul style="margin: 10px auto;">                        
                              <li>Allocation gap of 3 cases, POS value Rs 19.26 Lac</li>          
                              <li>Trail Gap of 2 cases, POS value Rs. 1.16 Lac</li>
                              <li>Trail Intensity is very low and less trail attempt on the assigned cases by FOS and found 3 cases are less than attempt 5</li>
                            </ul>
                          </li>
                          <li><strong>Information Security:</strong><br>
                            
                            <ul style="margin: 10px auto;">                                  
                              <li>USB access available in agency system (Only for Observation)</li>
                            </ul>
                          </li>
                          <li><strong>Agency Back Office:</strong><br>
                            
                            <ul style="margin: 10px auto;">                                  
                              <li>PVR not available for 2 FOS</li>
                            </ul>
                          </li>
                        </ul>
                        
                      </td>
                    </tr>
               
                  </tbody>
              </table> 


              <h4 style="color: brown; "> 5.4.2. Agency Global Informatics</h4>

              <table class="exceptional" width="100%" style="margin-bottom: 100px; text-align: left; border: 1px solid #ccc;">
                <thead>
                    <tr>
                        <th colspan="10">Table 17: Global Informatics - Product-Wise – Key Issues</th>
                    </tr>
                  <tr style="font-weight: bolder;">
                    <td style="width: 200px;">Product Name</td>
             
                    <td>Remarks</td>
                                            
                  </tr>
                </thead>
                <tbody>
                  <tr>                    
                    <th class="red"> Business Loan</th>               
                    <td style="text-align: left;">
                      <ul>
                        <li><strong>Allocation:</strong><br>
                          
                          <ul style="margin: 10px auto;">                        
                            <li>Allocation gap of 3 cases, POS value Rs 19.26 Lac</li>          
                            <li>Trail Gap of 2 cases, POS value Rs. 1.16 Lac</li>
                            <li>Trail Intensity is very low and less trail attempt on the assigned cases by FOS and found 3 cases are less than attempt 5</li>
                          </ul>
                        </li>
                        <li><strong>Information Security:</strong><br>
                          
                          <ul style="margin: 10px auto;">                                  
                            <li>USB access available in agency system (Only for Observation)</li>
                          </ul>
                        </li>
                        <li><strong>Agency Back Office:</strong><br>
                          
                          <ul style="margin: 10px auto;">                                  
                            <li>PVR not available for 2 FOS</li>
                          </ul>
                        </li>
                      </ul>
                      
                    </td>
                  </tr>
                  <tr>                    
                    <th class="red"> Micro Business Loan</th>               
                    <td style="text-align: left;">
                      <ul>
                        <li><strong>Allocation:</strong><br>
                          
                          <ul style="margin: 10px auto;">                        
                            <li>Allocation gap of 3 cases, POS value Rs 19.26 Lac</li>          
                            <li>Trail Gap of 2 cases, POS value Rs. 1.16 Lac</li>
                            <li>Trail Intensity is very low and less trail attempt on the assigned cases by FOS and found 3 cases are less than attempt 5</li>
                          </ul>
                        </li>
                        <li><strong>Information Security:</strong><br>
                          
                          <ul style="margin: 10px auto;">                                  
                            <li>USB access available in agency system (Only for Observation)</li>
                          </ul>
                        </li>
                        <li><strong>Agency Back Office:</strong><br>
                          
                          <ul style="margin: 10px auto;">                                  
                            <li>PVR not available for 2 FOS</li>
                          </ul>
                        </li>
                      </ul>
                      
                    </td>
                  </tr>
                  <tr>                    
                    <th class="red"> Two-Wheeler Loan</th>               
                    <td style="text-align: left;">
                      <ul>
                        <li><strong>Allocation:</strong><br>
                          
                          <ul style="margin: 10px auto;">                        
                            <li>Allocation gap of 3 cases, POS value Rs 19.26 Lac</li>          
                            <li>Trail Gap of 2 cases, POS value Rs. 1.16 Lac</li>
                            <li>Trail Intensity is very low and less trail attempt on the assigned cases by FOS and found 3 cases are less than attempt 5</li>
                          </ul>
                        </li>
                        <li><strong>Information Security:</strong><br>
                          
                          <ul style="margin: 10px auto;">                                  
                            <li>USB access available in agency system (Only for Observation)</li>
                          </ul>
                        </li>
                        <li><strong>Agency Back Office:</strong><br>
                          
                          <ul style="margin: 10px auto;">                                  
                            <li>PVR not available for 2 FOS</li>
                          </ul>
                        </li>
                      </ul>
                      
                    </td>
                  </tr>
                  <tr>                    
                    <th class="red">Consumer Loan</th>               
                    <td style="text-align: left;">
                      <ul>
                        <li><strong>Allocation:</strong><br>
                          
                          <ul style="margin: 10px auto;">                        
                            <li>Allocation gap of 3 cases, POS value Rs 19.26 Lac</li>          
                            <li>Trail Gap of 2 cases, POS value Rs. 1.16 Lac</li>
                            <li>Trail Intensity is very low and less trail attempt on the assigned cases by FOS and found 3 cases are less than attempt 5</li>
                          </ul>
                        </li>
                        <li><strong>Information Security:</strong><br>
                          
                          <ul style="margin: 10px auto;">                                  
                            <li>USB access available in agency system (Only for Observation)</li>
                          </ul>
                        </li>
                        <li><strong>Agency Back Office:</strong><br>
                          
                          <ul style="margin: 10px auto;">                                  
                            <li>PVR not available for 2 FOS</li>
                          </ul>
                        </li>
                      </ul>
                      
                    </td>
                  </tr>
             
                </tbody>
            </table> 


            
            <h4 style="color: brown; ">5.4.3. Agency Jai Sai Services</h4>

            <table class="exceptional" width="100%" style="margin-bottom: 100px; text-align: left; border: 1px solid #ccc;">
              <thead>
                  <tr>
                      <th colspan="10">Table 18: Jai Sai Services - Product- Wise – Key Issues</th>
                  </tr>
                <tr style="font-weight: bolder;">
                  <td style="width: 200px;">Product Name</td>
           
                  <td>Remarks</td>
                                          
                </tr>
              </thead>
              <tbody>
                <tr>                    
                  <th class="red">Personal Loan</th>               
                  <td style="text-align: left;">
                    <ul>
                      <li><strong>Allocation:</strong><br>
                        
                        <ul style="margin: 10px auto;">                        
                          <li>Allocation gap of 3 cases, POS value Rs 19.26 Lac</li>          
                          <li>Trail Gap of 2 cases, POS value Rs. 1.16 Lac</li>
                          <li>Trail Intensity is very low and less trail attempt on the assigned cases by FOS and found 3 cases are less than attempt 5</li>
                        </ul>
                      </li>
                      <li><strong>Information Security:</strong><br>
                        
                        <ul style="margin: 10px auto;">                                  
                          <li>USB access available in agency system (Only for Observation)</li>
                        </ul>
                      </li>
                      <li><strong>Agency Back Office:</strong><br>
                        
                        <ul style="margin: 10px auto;">                                  
                          <li>PVR not available for 2 FOS</li>
                        </ul>
                      </li>
                    </ul>
                    
                  </td>
                </tr>
               
                <tr>                    
                  <th class="red"> Two-Wheeler Loan</th>               
                  <td style="text-align: left;">
                    <ul>
                      <li><strong>Allocation:</strong><br>
                        
                        <ul style="margin: 10px auto;">                        
                          <li>Allocation gap of 3 cases, POS value Rs 19.26 Lac</li>          
                          <li>Trail Gap of 2 cases, POS value Rs. 1.16 Lac</li>
                          <li>Trail Intensity is very low and less trail attempt on the assigned cases by FOS and found 3 cases are less than attempt 5</li>
                        </ul>
                      </li>
                      <li><strong>Information Security:</strong><br>
                        
                        <ul style="margin: 10px auto;">                                  
                          <li>USB access available in agency system (Only for Observation)</li>
                        </ul>
                      </li>
                      <li><strong>Agency Back Office:</strong><br>
                        
                        <ul style="margin: 10px auto;">                                  
                          <li>PVR not available for 2 FOS</li>
                        </ul>
                      </li>
                    </ul>
                    
                  </td>
                </tr>
                <tr>                    
                  <th class="red"> Auto-Used Car</th>               
                  <td style="text-align: left;">
                    <ul>
                      <li><strong>Allocation:</strong><br>
                        
                        <ul style="margin: 10px auto;">                        
                          <li>Allocation gap of 3 cases, POS value Rs 19.26 Lac</li>          
                          <li>Trail Gap of 2 cases, POS value Rs. 1.16 Lac</li>
                          <li>Trail Intensity is very low and less trail attempt on the assigned cases by FOS and found 3 cases are less than attempt 5</li>
                        </ul>
                      </li>
                      <li><strong>Information Security:</strong><br>
                        
                        <ul style="margin: 10px auto;">                                  
                          <li>USB access available in agency system (Only for Observation)</li>
                        </ul>
                      </li>
                      <li><strong>Agency Back Office:</strong><br>
                        
                        <ul style="margin: 10px auto;">                                  
                          <li>PVR not available for 2 FOS</li>
                        </ul>
                      </li>
                    </ul>
                    
                  </td>
                </tr>
           
           
              </tbody>
          </table> 
            
          <h4 style="color: brown; ">5.4.4. Agency Siddhi Vinayaka</h4>

          <table class="exceptional" width="100%" style="margin-bottom: 100px; text-align: left; border: 1px solid #ccc;">
            <thead>
                <tr>
                    <th colspan="10">Table 9 : Siddhi Vinayaka- Product-Wise – Key Issues</th>
                </tr>
              <tr style="font-weight: bolder;">
                <td style="width: 200px;">Product Name</td>
         
                <td>Remarks</td>
                                        
              </tr>
            </thead>
            <tbody>
              <tr>                    
                <th class="red">Two-Wheeler Loan</th>               
                <td style="text-align: left;">
                  <ul>
                    <li><strong>Allocation:</strong><br>
                      
                      <ul style="margin: 10px auto;">                        
                        <li>Allocation gap of 3 cases, POS value Rs 19.26 Lac</li>          
                        <li>Trail Gap of 2 cases, POS value Rs. 1.16 Lac</li>
                        <li>Trail Intensity is very low and less trail attempt on the assigned cases by FOS and found 3 cases are less than attempt 5</li>
                      </ul>
                    </li>
                    <li><strong>Information Security:</strong><br>
                      
                      <ul style="margin: 10px auto;">                                  
                        <li>USB access available in agency system (Only for Observation)</li>
                      </ul>
                    </li>
                    <li><strong>Agency Back Office:</strong><br>
                      
                      <ul style="margin: 10px auto;">                                  
                        <li>PVR not available for 2 FOS</li>
                      </ul>
                    </li>
                  </ul>
                  
                </td>
              </tr>
             
          
         
         
            </tbody>
        </table> 


        <h3 style="color: brown; ">5.5. Repo Agency –:</h3>
        <h4 style="color: brown; ">5.5.1. Jai Sai Services</h4>

        <table class="exceptional" width="100%" style="margin-bottom: 100px; text-align: left; border: 1px solid #ccc;">
          <thead>
              <tr>
                  <th colspan="10">Table 19: Jai Sai Services - Product-Wise – Key Issues</th>
              </tr>
            <tr style="font-weight: bolder;">
              <td style="width: 200px;">Product Name</td>
       
              <td>Remarks</td>
                                      
            </tr>
          </thead>
          <tbody>
            <tr>                    
              <th class="red">Two-Wheeler Loan</th>               
              <td style="text-align: left;">
                <ul>
                            
                    
                      <li>Repo agency agreement not available</li>          
                      <li>Repo request not raised through Mail (Only for Observation)</li>
                  
                </ul>
                
              </td>
            </tr>
           
        
       
       
          </tbody>
      </table> 
      <h3 style="color: brown; ">5.6. Yard Agency – Product Wise</h3>
      <h4 style="color: brown; ">5.6.1. Kuldip Automobiles</h4>
      <table class="exceptional" width="100%" style="margin-bottom: 100px; text-align: left; border: 1px solid #ccc;">
        <thead>
            <tr>
                <th colspan="10">Table 20: Kuldip Automobiles- Product-Wise – Key Issues</th>
            </tr>
          <tr style="font-weight: bolder;">
            <td style="width: 200px;">Product Name</td>
     
            <td>Remarks</td>
                                    
          </tr>
        </thead>
        <tbody>
          <tr>                    
            <th class="red">Two-Wheeler Loan</th>               
            <td style="text-align: left;">
              <ul>
                          
                  
                    <li>Yard Insurance not available at yard</li>
                
              </ul>
              
            </td>
          </tr>
         
      
     
     
        </tbody>
    </table> 

                <table style="width: 100%; color: darkgoldenrod; text-align:left; font-weight:  500;">
                  <tbody>
                      <tr>
                          <td>Private & Confidential</td>
                          <td>Karnataka Bangalore Audit Report–Wave–VII- Q3_OCT [FY 2020-21]</td>
                          <td>4</td>
                      </tr>
                  </tbody>
              </table>
              </div>
          </div>                
      </div>
  </div>

</div>


  <div class="main-wrap">
          

    <!-- Header -->
    <div class="" id="main" style="display: block; ">            
        
        <div class="right" style="padding: 20px;     background: #cccd;        ">                    
           <div style="padding-left: 15px; background: #fff;">
      
                <div style="background: #fff; padding: 15px; border-left: 20px solid brown;">
                  <div class="" style="margin-bottom: 15px;">
                    <img src="logo.jpg" class="" alt="" style="height:  50px;"/>
                    <img src="logo-qdegrees.png" class="" alt="" style="height:  50px; float: right;"/>
                  </div>
                  <h3  style="color: #1d54c3; ">6. Score Card – Product-Wise</h3>

                  <table class="exceptional" width="100%" style="margin-bottom: 100px; text-align: left;">
                    <thead>
                      <tr>
                        <th colspan="8">Table 21: Karnataka – Bangalore - Branch Score Card Product-Wise</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <th>S.No.</th>
                        <th>Product </th>
                        <th>Scored </th>
                        <th>Scorable</th>
                        <th>Scored (%)</th>
                        <th>Collection Manager</th>
                        <th>Location  </th>                      
                        <th>Reporting Manager</th>                            
                      </tr>
                      <tr>
                        <td class="red">1</td>
                        <td class="red"> Business Loan </td>
                        <td class="red">48</td>
                        <td class="red">64 </td>

                        <td class="red">75.0%</td>
                        <td class="red">Ankit Kumar</td>
              
                        <td>Ranchi</td>
                        <td>Tanisha Chanda</td>

                      </tr>
                      <tr>
                        <td class="red">2</td>
                        <td class="red"> Consumer Loan</td>
                        <td class="red">48</td>
                        <td class="red">64 </td>

                        <td class="red">75.0%</td>
                        <td class="red">UTPAL GHOSH</td>
              
                        <td>Ranchi</td>
                        <td>Tanisha Chanda</td>

                      </tr>

                         <tr>
                        <td class="red">3</td>
                        <td class="red">Micro Business Loan </td>
                        <td class="red">48</td>
                        <td class="red">64 </td>

                        <td class="red">75.0%</td>
                        <td class="red">Ankit Kumar</td>
              
                        <td>Ranchi</td>
                        <td>Tanisha Chanda</td>

                      </tr>
                      <tr>
                        <td class="red">4</td>
                        <td class="red">MORT_HFC Audit</td>
                        <td class="red">48</td>
                        <td class="red">64 </td>

                        <td class="red">75.0%</td>
                        <td class="red">Ankit Kumar</td>
              
                        <td>Ranchi</td>
                        <td>Tanisha Chanda</td>

                      </tr>
                      <tr>
                        <td class="red">5</td>
                        <td class="red">Personal Loan </td>
                        <td class="red">48</td>
                        <td class="red">64 </td>

                        <td class="red">75.0%</td>
                        <td class="red">Ankit Kumar</td>
              
                        <td>Ranchi</td>
                        <td>Tanisha Chanda</td>

                      </tr>
                      <tr>
                        <td class="red">6</td>
                        <td class="red"> Two-Wheeler</td>
                        <td class="red">48</td>
                        <td class="red">64 </td>

                        <td class="red">75.0%</td>
                        <td class="red">Ankit Kumar</td>
              
                        <td>Ranchi</td>
                        <td>Tanisha Chanda</td>

                      </tr>
                      <tr>
                        <td class="red">7</td>
                        <td class="red">Auto Used Car </td>
                        <td class="red">48</td>
                        <td class="red">64 </td>

                        <td class="red">75.0%</td>
                        <td class="red">Ankit Kumar</td>
              
                        <td>Ranchi</td>
                        <td>Tanisha Chanda</td>

                      </tr>
                  
                    </tbody>
                </table>


                
              
              <h3  style="color: #1d54c3; ">7. Score Card – Collection Manager</h3>

              <table class="exceptional" width="100%" style="margin-bottom: 100px; text-align: left;">
                <thead>
                  <tr>
                    <th colspan="6">Table 23: Karnataka – Bangalore - Collection Manager Score Card</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <th>S.No.</th>
                    <th>Collection Manager </th>
                    <th>Products Handled </th>
                    <th>Scored</th>
                    <th>Scorable</th>
                    <th>Scored (%)</th>
                  </tr>
                  <tr>
                    <td class="red">1</td>
                    <td class="red">Ankit Kumar</td>
                    <td class="red">BL, MBL, MORT_HFC, PL, UC and TW</td>
                    <td class="red">541.2</td>
                    <td class="red">648 </td>
                    <td class="red">83.5%</td>
                  </tr>
                  <tr>
                    <td class="red">2</td>
                    <td class="red">UTPAL GHOSH</td>
                    <td class="red"> CL</td>
                    <td class="red">128</td>
                    <td class="red">158 </td>
                    <td class="red">81.0%</td>
                  </tr>
                </tbody>
            </table>
            
           

          <h3  style="color: #1d54c3; ">8. Score Card – Collection Agency/Repo/Yard Agency – Product-Wise</h3>

          <table class="exceptional" width="100%" style="margin-bottom: 100px; text-align: left;">
            <thead>
              <tr>
                <th colspan="7">Table 25: Karnataka – Bangalore – Collection /Repo/ Yard Agency - Score Card – Product -Wise</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <th>S.No.</th>
                <th>Agency Type </th>
                <th>Product </th>
                <th>Agency Name</th>
                <th>Scored</th>
                <th>Scorable</th>
                <th>Scored (%)</th>
              </tr>
              <tr>
                <td class="red">1</td>
                <td class="red">Collection Agency</td>
                <td class="red">Business Loan</td>
                <td class="red">FAIR ADVISORY AND LEGALE</td>
                <td class="red">68 </td>
                <td class="red">79</td>
                <td class="red">86.1%</td>
                
              </tr>
              <tr>
                <td class="red">2</td>
                <td class="red">Micro Business Loan</td>
                <td class="red">Business Loan</td>
                
                <td class="red">Global Informatics</td>
                <td class="red">27</td>
                <td class="red">32</td>
                <td class="red">84.4%</td>
               
              </tr>
              <tr>
                <td class="red">3</td>
                <td class="red">Collection Agency</td>
                <td class="red">Business Loan</td>
                <td class="red">FAIR ADVISORY AND LEGALE</td>
                <td class="red">69 </td>
                <td class="red">79</td>
                <td class="red">87.3%</td>
              </tr>
              <tr>
                <td class="red">4</td>
                <td class="red">Collection Agency</td>
                <td class="red">Business Loan</td>
                <td class="red">FAIR ADVISORY AND LEGALE</td>
                <td class="red">68 </td>
                <td class="red">79</td>
                <td class="red">86.1%</td>
                
              </tr>
              <tr>
                <td class="red">5</td>
                <td class="red">Micro Business Loan</td>
                <td class="red">Business Loan</td>
                
                <td class="red">Global Informatics</td>
                <td class="red">27</td>
                <td class="red">32</td>
                <td class="red">84.4%</td>
               
              </tr>
              <tr>
                <td class="red">6</td>
                <td class="red">Collection Agency</td>
                <td class="red">Business Loan</td>
                <td class="red">FAIR ADVISORY AND LEGALE</td>
                <td class="red">69 </td>
                <td class="red">79</td>
                <td class="red">87.3%</td>
              </tr>
              <tr>
                <td class="red">7</td>
                <td class="red">Collection Agency</td>
                <td class="red">Business Loan</td>
                <td class="red">FAIR ADVISORY AND LEGALE</td>
                <td class="red">68 </td>
                <td class="red">79</td>
                <td class="red">86.1%</td>
                
              </tr>
              <tr>
                <td class="red">8</td>
                <td class="red">Micro Business Loan</td>
                <td class="red">Business Loan</td>
                
                <td class="red">Global Informatics</td>
                <td class="red">27</td>
                <td class="red">32</td>
                <td class="red">84.4%</td>
               
              </tr>
              <tr>
                <td class="red">9</td>
                <td class="red">Collection Agency</td>
                <td class="red">Business Loan</td>
                <td class="red">FAIR ADVISORY AND LEGALE</td>
                <td class="red">69 </td>
                <td class="red">79</td>
                <td class="red">87.3%</td>
              </tr>
              
            </tbody>
        </table>
        
      <h3  style="color: #1d54c3; ">9. Annexure</h3>
      <h3 style="color: brown; ">9.1. List of Branches Audited</h3>
      <table class="exceptional" width="100%" style="margin-bottom: 100px; text-align: left; border: 1px solid #ccc;">
        <thead>
            <tr>
                <th colspan="5">Table 7 : Karnataka – Bangalore - List of Branches Covered</th>
            </tr>
          <tr style="font-weight: bolder;">
            <td>S. No.</td>
            <td>City Name</td>     
            <td colspan="3">Collection Manager</td>
                                    
          </tr>
        </thead>
        <tbody>
          <tr>
            <th class="red"> 1</th>
            <th class="red">Banglore</th>     
            <td style="text-align: left;" colspan="3">
              <ul>
                <li>                 
                  <ul>                                  
                    <li>Amul Kanawade</li>
                    <li>Mounesh Kammar</li>
                    <li>Amul Kanawade</li>
                    <li>Mounesh Kammar</li>
                    <li>Amul Kanawade</li>
                    <li>Mounesh Kammar</li>
                    <li>Amul Kanawade</li>
                    <li>Mounesh Kammar</li>
                    <li>Amul Kanawade</li>
                    <li>Mounesh Kammar</li>
                  </ul>
                </li>
              </ul>
            </td>
          </tr>
          </tr>
        </tbody>
    </table>




    <h3 style="color: brown; ">9.2. List of Collection Agencies Audited</h3>
    <table class="exceptional" width="100%" style="margin-bottom: 100px; text-align: left; border: 1px solid #ccc;">
      <thead>
          <tr>
              <th colspan="8">Table 7 : FAIR ADVISORY AND LEGALE - Product-Wise – Key Issues</th>
          </tr>
        <tr style="font-weight: bolder;">
          <td>S. No.</td>
          <td>City Name</td>     
          <td colspan="3">Collection Agency Name</td>
          <td colspan="3">Products Covered</td>                         
        </tr>
      </thead>
      <tbody>
        <tr>
          <th class="red"> 1</th>
          <th class="red">Bangalore</th>     
          <td>FAIR ADVISORY AND LEGALE</td>
          <td style="text-align: left;" colspan="3">
            <ul>
                                       
                  <li>Business Loan, Micro Business Loan, Two-Wheeler and Consumer Loan</li>
          
            </ul>
          </td>
        </tr>
        <tr>
          <th class="red"> 2</th>
          <th class="red">Bangalore</th>     
          <td>Global Informatics</td>
          <td style="text-align: left;" colspan="3">
            <ul>
                                              
                  <li>Consumer Loan, Micro Business Loan, MORT_HFC and Business Loan</li>
              
            </ul>
          </td>
        </tr>
        <tr>
          <th class="red"> 1</th>
          <th class="red">Bangalore</th>     
          <td>Jai Sai Services</td>
          <td style="text-align: left;" colspan="3">
            <ul>
          
                  <li>Business Loan, Micro Business Loan, Two-Wheeler and Consumer Loan</li>
            
            </ul>
          </td>
        </tr>
        <tr>
          <th class="red"> 3</th>
          <th class="red">Bangalore</th>     
          <td>Siddhi Vinayaka</td>
          <td style="text-align: left;" colspan="3">
            <ul>
                                            
                  <li>Two-Wheeler</li>
            
            </ul>
          </td>
        </tr>
        <tr>
          <th class="red"> 4</th>
          <th class="red">Bangalore</th>     
          <td>FAIR ADVISORY AND LEGALE</td>
          <td style="text-align: left;" colspan="3">
            <ul>
                                         
                  <li>Business Loan, Micro Business Loan, Two-Wheeler and Consumer Loan</li>
               
            </ul>
          </td>
        </tr>
        <tr>
          <th class="red"> 7</th>
          <th class="red">Bangalore</th>     
          <td>FAIR ADVISORY AND LEGALE</td>
          <td style="text-align: left;" colspan="3">
            <ul>
                                         
                  <li>Business Loan, Micro Business Loan, Two-Wheeler and Consumer Loan</li>
               
            </ul>
          </td>
        </tr>
        <tr>
          <th class="red"> 8</th>
          <th class="red">Bangalore</th>     
          <td>Fair Advisory Legal Services</td>
          <td style="text-align: left;" colspan="3">
            <ul>
                                       
                  <li>Personal Loan</li>
               
            </ul>
          </td>
        </tr>
      </tbody>
  </table>
  <h3 style="color: brown; ">9.3. List of Yard Agencies Audited</h3>
  <table class="exceptional" width="100%" style="margin-bottom: 100px; text-align: left; border: 1px solid #ccc;">
    <thead>
        <tr>
            <th colspan="8">Table 29: Karnataka – Bangalore - List of Collection Agencies</th>
        </tr>
      <tr style="font-weight: bolder;">
        <td>S. No.</td>
        <td>City</td>
        <td>Yard Agency Name</td>
        <td>Products Covered</td>        
      </tr>
    </thead>
    <tbody>
      <tr>
        <th class="red"> 1</th>
        <th class="red">Bangalore</th>
        <td>Ruby Enterprises</td>
        <td>
          <ul style="text-align: left;">
            <li>Auto/Used Car Loan</li>
            <li>Fresh Recovery</li>
            <li>Two Wheeler Loan</li>
            <li>Vintage Recovery</li>
          </ul>
        </td>
      </tr>
      <tr>
        <th class="red"> 2</th>
        <th class="red">Bangalore</th>
        <td>Sams Securities And Yard Contractors</td>
        <td>
          <ul style="text-align: left;">     
            <li>Fresh Recovery</li>
            <li>Two Wheeler Loan</li>
            <li>Vintage Recovery</li>
          </ul>
        </td>
      </tr>
    </tbody>
</table>
  <h3 style="color: brown; ">9.4. Audit Team</h3>
    <table class="exceptional" width="100%" style="margin-bottom: 100px; text-align: left; border: 1px solid #ccc;">
      <thead>
          <tr>
              <th colspan="8">Table 29: Qdegrees Audit Team</th>
          </tr>
        <tr style="font-weight: bolder;">
          <td>S. No.</td>
          <td>Auditor Name</td>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th class="red"> 1</th>
          <th class="red">Rajiv Sonkar</th>
        </tr>
      </tbody>
  </table>

  <h3 style="color: brown; ">9.4. Visiting Details</h3>
    <table class="exceptional" width="100%" style="margin-bottom: 100px; text-align: left; border: 1px solid #ccc;">
      <thead>
          <tr>
              <th colspan="8">Table 30: Jharkhand- Qdegrees Team Audit Visiting Details</th>
          </tr>
        <tr style="font-weight: bolder;">
          <td>S. No.</td>
          <td>State</td>
          <td>City</td>
          <td>Branch and Collection Agency Name</td>
          <td>Product</td>
          <td>Visiting Date</td>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th class="red"> 1</th>
          <td class="red">Jharkhand</td>
          <td class="red">Jamshedpur</td>
          <td class="red">BRANCH Jamshedpur</td>
          <td class="red">Business Loan</td>
          <td class="red">10-Feb-20</td>
        </tr>      
        <tr>
          <th class="red"> 2</th>
          <td class="red">Jharkhand</td>
          <td class="red">Jamshedpur</td>
          <td class="red">BRANCH Jamshedpur</td>
          <td class="red">Business Loan</td>
          <td class="red">10-Feb-20</td>
        </tr>
        <tr>
          <th class="red"> 3</th>
          <td class="red">Jharkhand</td>
          <td class="red">Jamshedpur</td>
          <td class="red">BRANCH Jamshedpur</td>
          <td class="red">Business Loan</td>
          <td class="red">10-Feb-20</td>
        </tr>
        <tr>
          <th class="red"> 4</th>
          <td class="red">Jharkhand</td>
          <td class="red">Jamshedpur</td>
          <td class="red">BRANCH Jamshedpur</td>
          <td class="red">Business Loan</td>
          <td class="red">10-Feb-20</td>
        </tr>
        <tr>
          <th class="red"> 5</th>
          <td class="red">Jharkhand</td>
          <td class="red">Jamshedpur</td>
          <td class="red">BRANCH Jamshedpur</td>
          <td class="red">Business Loan</td>
          <td class="red">10-Feb-20</td>
        </tr>     
      </tbody>
  </table>
  <h3 style="color: brown; ">9.5. Abbreviations</h3>
  <table class="exceptional" width="100%" style="margin-bottom: 100px; text-align: left; border: 1px solid #ccc;">
    <thead>
        <tr>
            <th colspan="8">Table 31: Abbreviations</th>
        </tr>
      <tr style="font-weight: bolder;">
        <td>Short Name</td>
        <td>Full Form</td>
      </tr>
    </thead>
    <tbody>
      <tr>
        <th class="red">NPA</th>
        <th class="red">Non-Preforming Assets</th>
      </tr>
      <tr>
        <th class="red">PL</th>
        <th class="red">Personal Loan   </th>
      </tr>
      <tr>
        <th class="red">CL</th>
        <th class="red">Consumer Loan</th>
      </tr>
      <tr>
        <th class="red">MBL</th>
        <th class="red">Micro Business Loan</th>
      </tr>
      <tr>
        <th class="red">BL</th>
        <th class="red">Business Loan</th>
      </tr>
      <tr>
        <th class="red">X-Sell</th>
        <th class="red">Cross Sell</th>
      </tr>
      <tr>
        <th class="red">TW</th>
        <th class="red">Two-Wheeler</th>
      </tr>
      <tr>
        <th class="red">UC</th>
        <th class="red">Auto/Used Car</th>
      </tr>
      <tr>
        <th class="red">ACR</th>
        <th class="red">Account to Collector Ratio</th>
      </tr>
      <tr>
        <th class="red">FOS</th>
        <th class="red">Feet on Street</th>
      </tr>


      
    </tbody>
</table>
                  <table style="width: 100%; color: darkgoldenrod; text-align:left; font-weight:  500;">
                    <tbody>
                        <tr>
                            <td>Private & Confidential</td>
                            <td>Karnataka Bangalore Audit Report–Wave–VII- Q3_OCT [FY 2020-21]</td>
                            <td>4</td>
                        </tr>
                    </tbody>
                </table>
                </div>
            </div>                
        </div>
    </div>

</div>

        <h6 style="font-size:16px; font-family: arial regular; font-weight: 100; text-align: center; margin-top:50px; margin-bottom:50px;">For Any Query Regarding This Kindly Visit Us - <a href="https://www.qdegrees.com/contact-us" target="_blank">www.qdegrees.com/contact-us</a></h6>
        <script src="https://code.highcharts.com/highcharts.js"></script>
        <script src="https://code.highcharts.com/modules/data.js"></script>
        <script src="https://code.highcharts.com/modules/drilldown.js"></script>
        <script src="https://code.highcharts.com/modules/exporting.js"></script>
        <script src="https://code.highcharts.com/modules/export-data.js"></script>
        <script src="https://code.highcharts.com/modules/accessibility.js"></script>


        <script>
          // Create the chart
Highcharts.chart('container', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'Banglore'
    },
    subtitle: {
        text: ''
    },
    accessibility: {
        announceNewData: {
            enabled: true
        }
    },
    xAxis: {
        type: 'category'
    },
    yAxis: {
        title: {
            text: 'Total percent market share'
        }

    },
    legend: {
        enabled: false
    },
    plotOptions: {
        series: {
            borderWidth: 0,
            dataLabels: {
                enabled: true,
                format: '{point.y:.1f}%'
            }
        }
    },

    tooltip: {
        headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
        pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}%</b> of total<br/>'
    },

    series: [
        {
            name: "Browsers",
            colorByPoint: true,
            data: [
                {
                    name: "Wave - II",
                    y: 92.74,                  
                },
                {
                    name: "Wave - III",
                    y: 50.57,
                    
                },
                {
                    name: "Wave - IV",
                    y: 40.23,
                   
                },
                {
                    name: "Wave - VII",
                    y: 67.58,
                    
                }
            ]
        }
    ],
});
        </script>
      </body>


   