<?php 
if($teeth_record){
    foreach($teeth_record as $row){
        if($row->ill_type == 3){
?>
<div class="inputfield" id="build">
    <div class="inputfield">
        <div class="textfield btm20padding" style = "background: #bce8f1;padding:3px 8px 5px;">
            <div class="checkbox-container inrow">
                <label class="checkbox-label">
                    <input type="checkbox" <?php if($row->partial == 1){echo "checked='checked'";} ?>  id="partial" name="partial" value="1">
                    <span class="checkbox-custom rectangular"></span>
                    <span class='clabel'><?=$this->lang->line('partial')?> </span>
                </label>
            </div>
            <div class="checkbox-container inrow">
                <label class="checkbox-label">
                    <input type="checkbox" <?php if($row->complete == 1){echo "checked='checked'";} ?>  id="complete" name="complete" value="1">
                    <span class="checkbox-custom rectangular"></span>
                    <span class='clabel'><?=$this->lang->line('complete')?> </span>
                </label>
            </div>
            <div class="checkbox-container inrow">
                <label class="checkbox-label">
                    <input type="checkbox" <?php if($row->implent == 1){echo "checked='checked'";} ?>  id="implent" name="implent" value="1">
                    <span class="checkbox-custom rectangular"></span>
                    <span class='clabel'><?=$this->lang->line('implent')?> </span>
                </label>
            </div>
        </div>
        <div class="textfield btm20padding" style = "background: #bce8f1;padding:3px 8px 5px;">
            <div class="checkbox-container inrow">
                <label class="checkbox-label">
                    <input type="checkbox" <?php if($row->ccpalete == 1){echo "checked='checked'";} ?>  id="ccpalet" name="ccpalet" value="1">
                    <span class="checkbox-custom rectangular"></span>
                    <span class='clabel'><?=$this->lang->line('ccpalet')?> </span>
                </label>
            </div>
            <div class="checkbox-container inrow">
                <label class="checkbox-label">
                    <input type="checkbox" <?php if($row->full_bredge == 1){echo "checked='checked'";} ?>  id="fulbredge" name="fulbredge" value="1">
                    <span class="checkbox-custom rectangular"></span>
                    <span class='clabel'><?=$this->lang->line('fulbredge')?> </span>
                </label>
            </div>
        </div>
    </div>
    <table>
        <input type="hidden" class="build_urn" name="build_urn" value="<?=$row->urn?>">
        <!--*****************************btop Teeth*****************************-->
        <tr style="border-bbottom:1px solid #000;">
            <!--*****************************btop Right Teeth*****************************-->
            <td style="border-left:1px solid #000; padding-bbottom:5px;">
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>8</stron></span><br>
                    <label class="checkbox-labels">
                        <input type="checkbox" class="btop" <?php if($row->topright8 == 1){echo "checked='checked'";} ?> name="btopr8" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>7</stron></span><br>
                    <label class="checkbox-labels">
                        <input type="checkbox" class="btop" <?php if($row->topright7 == 1){echo "checked='checked'";} ?> name="btopr7" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>6</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="btop" <?php if($row->topright6 == 1){echo "checked='checked'";} ?> name="btopr6" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>5</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="btop" <?php if($row->topright5 == 1){echo "checked='checked'";} ?> name="btopr5" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>4</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="btop" <?php if($row->topright4 == 1){echo "checked='checked'";} ?> name="btopr4" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>3</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="btop" <?php if($row->topright3 == 1){echo "checked='checked'";} ?> name="btopr3" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>2</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="btop" <?php if($row->topright2 == 1){echo "checked='checked'";} ?> name="btopr2" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows" style="margin-left:10px">
                    <span class='toplabel'><strong>1</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="btop" <?php if($row->topright1 == 1){echo "checked='checked'";} ?> name="btopr1" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
            </td>
            
            <!--*****************************btop left Teeth*****************************-->
            <td style="padding-bbottom:5px;">
                <div class="checkbox-container inrows" style="margin-right:10px;">
                    <span class='toplabel'><strong>1</stron></span><br>
                    <label class="checkbox-labels">
                        <input type="checkbox" class="btop" <?php if($row->topleft1 == 1){echo "checked='checked'";} ?> name="btopl1" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>2</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="btop" <?php if($row->topleft2 == 1){echo "checked='checked'";} ?> name="btopl2" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>3</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="btop" <?php if($row->topleft3 == 1){echo "checked='checked'";} ?> name="btopl3" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>4</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="btop" <?php if($row->topleft4 == 1){echo "checked='checked'";} ?> name="btopl4" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>5</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="btop" <?php if($row->topleft5 == 1){echo "checked='checked'";} ?> name="btopl5" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>6</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="btop" <?php if($row->topleft6 == 1){echo "checked='checked'";} ?> name="btopl6" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>7</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="btop" <?php if($row->topleft7 == 1){echo "checked='checked'";} ?> name="btopl7" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>8</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="btop" <?php if($row->topleft8 == 1){echo "checked='checked'";} ?> name="btopl8" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows" style="margin-left:0;">
                    <span><strong><?=$this->lang->line("all");?></stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="btopall" id="btopall" onclick="selectAll('btopall','btop');" name="btopall" value="all" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>   
                </div>
            </td>
        </tr>
        
        <!--*****************************bbottom Teeth*****************************-->
        <tr>
            <!--*****************************bbottom Right Teeth*****************************-->
            <td style="border-left:1px solid #000;padding-btop:5px;">
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>8</stron></span><br>
                    <label class="checkbox-labels">
                        <input type="checkbox" class="bbottom" <?php if($row->bottomright8 == 1){echo "checked='checked'";} ?> name="bbottomr8" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>7</stron></span><br>
                    <label class="checkbox-labels">
                        <input type="checkbox" class="bbottom" <?php if($row->bottomright7 == 1){echo "checked='checked'";} ?> name="bbottomr7" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>6</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="bbottom" <?php if($row->bottomright6 == 1){echo "checked='checked'";} ?> name="bbottomr6" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>5</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="bbottom" <?php if($row->bottomright5 == 1){echo "checked='checked'";} ?> name="bbottomr5" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>4</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="bbottom" <?php if($row->bottomright4 == 1){echo "checked='checked'";} ?> name="bbottomr4" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>3</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="bbottom" <?php if($row->bottomright3 == 1){echo "checked='checked'";} ?> name="bbottomr3" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>2</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="bbottom" <?php if($row->bottomright2 == 1){echo "checked='checked'";} ?> name="bbottomr2" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows" style="margin-left:10px">
                    <span class='toplabel'><strong>1</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="bbottom" <?php if($row->bottomright1 == 1){echo "checked='checked'";} ?> name="bbottomr1" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
            </td>
            
            <!--*****************************bbottom Left Teeth*****************************-->
            <td style="padding-btop:5px;">
                <div class="checkbox-container inrows" style="margin-right:10px;">
                    <span class='toplabel'><strong>1</stron></span><br>
                    <label class="checkbox-labels">
                        <input type="checkbox" class="bbottom" <?php if($row->bottomleft1 == 1){echo "checked='checked'";} ?> name="bbottoml1" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>2</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="bbottom" <?php if($row->bottomleft2 == 1){echo "checked='checked'";} ?> name="bbottoml2" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>3</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="bbottom" <?php if($row->bottomleft3 == 1){echo "checked='checked'";} ?> name="bbottoml3" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>4</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="bbottom" <?php if($row->bottomleft4 == 1){echo "checked='checked'";} ?> name="bbottoml4" value="1"> 
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>5</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="bbottom" <?php if($row->bottomleft5 == 1){echo "checked='checked'";} ?> name="bbottoml5" value="1" >
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>6</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="bbottom" <?php if($row->bottomleft6 == 1){echo "checked='checked'";} ?> name="bbottoml6" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>7</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="bbottom" <?php if($row->bottomleft7 == 1){echo "checked='checked'";} ?> name="bbottoml7" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>                                                          
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>8</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="bbottom" <?php if($row->bottomleft8 == 1){echo "checked='checked'";} ?> name="bbottoml8" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows" style="margin-left:0;">
                    <span><strong><?=$this->lang->line("all");?></stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="bbottomall" id="bbottomall" onclick="selectAll('bbottomall','bbottom');" name="bbottomlall" value="all">
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