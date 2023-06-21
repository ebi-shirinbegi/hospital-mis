<?php 
if($teeth_record){
    //echo "<pre>";print_r($teeth_record);exit;
    foreach($teeth_record as $row){
        if($row->ill_type == 7){
            //echo $row->urn;exit;
?>  
<div class="inputfield" id="fill">
    <table>
        <input type="hidden" class="exo_urn" name="exo_urn" value="<?=$row->urn?>">
        <!--*****************************Top Teeth*****************************-->
        <tr style="border-bottom:1px solid #000;">
            <!--*****************************Top Right Teeth*****************************-->
            <td style="border-left:1px solid #000; padding-bottom:5px;">
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>8</stron></span><br>
                    <label class="checkbox-labels">
                        <input type="checkbox" class="etop" <?php if($row->topright8 == 1){echo "checked='checked'";} ?> name="etopr8" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>7</stron></span><br>
                    <label class="checkbox-labels">
                        <input type="checkbox" class="etop" <?php if($row->topright7 == 1){echo "checked='checked'";} ?> name="etopr7" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>6</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="etop" <?php if($row->topright6 == 1){echo "checked='checked'";} ?> name="etopr6" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>5</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="etop" <?php if($row->topright5 == 1){echo "checked='checked'";} ?> name="etopr5" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>4</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="etop" <?php if($row->topright4 == 1){echo "checked='checked'";} ?> name="etopr4" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>3</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="etop" <?php if($row->topright3 == 1){echo "checked='checked'";} ?> name="etopr3" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>2</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="etop" <?php if($row->topright2 == 1){echo "checked='checked'";} ?> name="etopr2" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows" style="margin-left:10px">
                    <span class='toplabel'><strong>1</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="etop" <?php if($row->topright1 == 1){echo "checked='checked'";} ?> name="etopr1" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
            </td>
            
            <!--*****************************Top left Teeth*****************************-->
            <td style="padding-bottom:5px;">
                <div class="checkbox-container inrows" style="margin-right:10px;">
                    <span class='toplabel'><strong>1</stron></span><br>
                    <label class="checkbox-labels">
                        <input type="checkbox" class="etop" <?php if($row->topleft1 == 1){echo "checked='checked'";} ?> name="etopl1" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>2</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="etop" <?php if($row->topleft2 == 1){echo "checked='checked'";} ?> name="etopl2" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>3</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="etop" <?php if($row->topleft3 == 1){echo "checked='checked'";} ?> name="etopl3" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>4</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="etop" <?php if($row->topleft4 == 1){echo "checked='checked'";} ?> name="etopl4" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>5</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="etop" <?php if($row->topleft5 == 1){echo "checked='checked'";} ?> name="etopl5" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>6</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="etop" <?php if($row->topleft6 == 1){echo "checked='checked'";} ?> name="etopl6" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>7</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="etop" <?php if($row->topleft7 == 1){echo "checked='checked'";} ?> name="etopl7" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>8</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="etop" <?php if($row->topleft8 == 1){echo "checked='checked'";} ?> name="etopl8" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows" style="margin-left:0;">
                    <span><strong><?=$this->lang->line("all");?></stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="etopall" id="etopall" onclick="selectAll('etopall','etop');" name="etopall" value="all" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>   
                </div>
            </td>
        </tr>
        
        <!--*****************************Bottom Teeth*****************************-->
        <tr>
            <!--*****************************bottom Right Teeth*****************************-->
            <td style="border-left:1px solid #000;padding-top:5px;">
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>8</stron></span><br>
                    <label class="checkbox-labels">
                        <input type="checkbox" class="ebottom" <?php if($row->bottomright8 == 1){echo "checked='checked'";} ?> name="ebottomr8" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>7</stron></span><br>
                    <label class="checkbox-labels">
                        <input type="checkbox" class="ebottom" <?php if($row->bottomright7 == 1){echo "checked='checked'";} ?> name="ebottomr7" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>6</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="ebottom" <?php if($row->bottomright6 == 1){echo "checked='checked'";} ?> name="ebottomr6" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>5</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="ebottom" <?php if($row->bottomright5 == 1){echo "checked='checked'";} ?> name="ebottomr5" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>4</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="ebottom" <?php if($row->bottomright4 == 1){echo "checked='checked'";} ?> name="ebottomr4" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>3</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="ebottom" <?php if($row->bottomright3 == 1){echo "checked='checked'";} ?> name="ebottomr3" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>2</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="ebottom" <?php if($row->bottomright2 == 1){echo "checked='checked'";} ?> name="ebottomr2" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows" style="margin-left:10px">
                    <span class='toplabel'><strong>1</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="ebottom" <?php if($row->bottomright1 == 1){echo "checked='checked'";} ?> name="ebottomr1" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
            </td>
            
            <!--*****************************Bottom Left Teeth*****************************-->
            <td style="padding-top:5px;">
                <div class="checkbox-container inrows" style="margin-right:10px;">
                    <span class='toplabel'><strong>1</stron></span><br>
                    <label class="checkbox-labels">
                        <input type="checkbox" class="ebottom" <?php if($row->bottomleft1 == 1){echo "checked='checked'";} ?> name="ebottoml1" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>2</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="ebottom" <?php if($row->bottomleft2 == 1){echo "checked='checked'";} ?> name="ebottoml2" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>3</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="ebottom" <?php if($row->bottomleft3 == 1){echo "checked='checked'";} ?> name="ebottoml3" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>4</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="ebottom" <?php if($row->bottomleft4 == 1){echo "checked='checked'";} ?> name="ebottoml4" value="1"> 
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>5</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="ebottom" <?php if($row->bottomleft5 == 1){echo "checked='checked'";} ?> name="ebottoml5" value="1" >
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>6</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="ebottom" <?php if($row->bottomleft6 == 1){echo "checked='checked'";} ?> name="ebottoml6" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>7</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="ebottom" <?php if($row->bottomleft7 == 1){echo "checked='checked'";} ?> name="ebottoml7" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>8</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="ebottom" <?php if($row->bottomleft8 == 1){echo "checked='checked'";} ?> name="ebottoml8" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows" style="margin-left:0;">
                    <span><strong><?=$this->lang->line("all");?></stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="ebottomall" id="ebottomall" onclick="selectAll('ebottomall','ebottom');" name="ebottomlall" value="all">
                        <span class="checkbox-custom rectangular"></span>
                    </label>   
                </div>
            </td>
        </tr>
    </table>
</div>

<?php
        }
    }
}
?>