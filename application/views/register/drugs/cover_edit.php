<?php 
if($teeth_record){
    foreach($teeth_record as $row){
        if($row->ill_type == 2){
?>
<div class="inputfield" id="cover">
    <div class="inputfield">
        <div class="textfield btm20padding" style = "background: #d6e9c6;padding:3px 8px 5px;">
            <div class="checkbox-container inrow">
                <label class="checkbox-label">
                    <input type="checkbox" <?php if($row->golden == 1){echo "checked='checked'";} ?>  name="golden" value="1">
                    <span class="checkbox-custom rectangular"></span>
                    <span class='clabel'><?=$this->lang->line('golden')?> </span>
                </label>
            </div>
            <div class="checkbox-container inrow">
                <label class="checkbox-label">
                    <input type="checkbox" <?php if($row->silver == 1){echo "checked='checked'";} ?>  name="silver" value="1">
                    <span class="checkbox-custom rectangular"></span>
                    <span class='clabel'><?=$this->lang->line('silver')?> </span>
                </label>
            </div>
            <div class="checkbox-container inrow">
                <label class="checkbox-label">
                    <input type="checkbox" <?php if($row->same_color == 1){echo "checked='checked'";} ?> name="samecolor" value="1">
                    <span class="checkbox-custom rectangular"></span>
                    <span class='clabel'><?=$this->lang->line('samecolor')?> </span>
                </label>
            </div>
        </div>
        <div class="textfield btm20padding" style = "background: #d6e9c6;padding:3px 8px 5px;"> 
            <div class="checkbox-container inrow">
                <label class="checkbox-label">
                    <input type="checkbox" <?php if($row->zarconiam == 1){echo "checked='checked'";} ?>  name="zarconiam" value="1">
                    <span class="checkbox-custom rectangular"></span>
                    <span class='clabel'><?=$this->lang->line('zarconiam')?> </span>
                </label>
            </div>
            <div class="checkbox-container inrow">
                <label class="checkbox-label">
                    <input type="checkbox" <?php if($row->mx == 1){echo "checked='checked'";} ?> name="mx" value="1">
                    <span class="checkbox-custom rectangular"></span>
                    <span class='clabel'><?=$this->lang->line('mx')?> </span>
                </label>
            </div>
        </div>
    </div>
    <table>
        <input type="hidden" class="cover_urn" name="cover_urn" value="<?=$row->urn?>">
        <!--*****************************ctop Teeth*****************************-->
        <tr style="border-cbottom:1px solid #000;">
            <!--*****************************ctop Right Teeth*****************************-->
            <td style="border-left:1px solid #000; padding-cbottom:5px;">
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>8</stron></span><br>
                    <label class="checkbox-labels">
                        <input type="checkbox" class="ctop" <?php if($row->topright8 == 1){echo "checked='checked'";} ?> name="ctopr8" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>7</stron></span><br>
                    <label class="checkbox-labels">
                        <input type="checkbox" class="ctop" <?php if($row->topright7 == 1){echo "checked='checked'";} ?> name="ctopr7" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>6</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="ctop" <?php if($row->topright6 == 1){echo "checked='checked'";} ?> name="ctopr6" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>5</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="ctop" <?php if($row->topright5 == 1){echo "checked='checked'";} ?> name="ctopr5" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>4</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="ctop" <?php if($row->topright4 == 1){echo "checked='checked'";} ?> name="ctopr4" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>3</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="ctop" <?php if($row->topright3 == 1){echo "checked='checked'";} ?> name="ctopr3" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>2</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="ctop" <?php if($row->topright2 == 1){echo "checked='checked'";} ?> name="ctopr2" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows" style="margin-left:10px">
                    <span class='toplabel'><strong>1</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="ctop" <?php if($row->topright1 == 1){echo "checked='checked'";} ?> name="ctopr1" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
            </td>
            
            <!--*****************************ctop left Teeth*****************************-->
            <td style="padding-cbottom:5px;">
                <div class="checkbox-container inrows" style="margin-right:10px;">
                    <span class='toplabel'><strong>1</stron></span><br>
                    <label class="checkbox-labels">
                        <input type="checkbox" class="ctop" <?php if($row->topleft1 == 1){echo "checked='checked'";} ?> name="ctopl1" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>2</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="ctop" <?php if($row->topleft2 == 1){echo "checked='checked'";} ?> name="ctopl2" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>3</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="ctop" <?php if($row->topleft3 == 1){echo "checked='checked'";} ?> name="ctopl3" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>4</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="ctop" <?php if($row->topleft4 == 1){echo "checked='checked'";} ?> name="ctopl4" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>5</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="ctop" <?php if($row->topleft5 == 1){echo "checked='checked'";} ?> name="ctopl5" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>6</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="ctop" <?php if($row->topleft6 == 1){echo "checked='checked'";} ?> name="ctopl6" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>7</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="ctop" <?php if($row->topleft7 == 1){echo "checked='checked'";} ?> name="ctopl7" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>8</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="ctop" <?php if($row->topleft8 == 1){echo "checked='checked'";} ?> name="ctopl8" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows" style="margin-left:0;">
                    <span><strong><?=$this->lang->line("all");?></stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="ctopall" id="ctopall" onclick="selectAll('ctopall','ctop');" name="ctopall" value="all" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>   
                </div>
            </td>
        </tr>
        
        <!--*****************************cbottom Teeth*****************************-->
        <tr>
            <!--*****************************cbottom Right Teeth*****************************-->
            <td style="border-left:1px solid #000;padding-ctop:5px;">
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>8</stron></span><br>
                    <label class="checkbox-labels">
                        <input type="checkbox" class="cbottom" <?php if($row->bottomright8 == 1){echo "checked='checked'";} ?> name="cbottomr8" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>7</stron></span><br>
                    <label class="checkbox-labels">
                        <input type="checkbox" class="cbottom" <?php if($row->bottomright7 == 1){echo "checked='checked'";} ?> name="cbottomr7" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>6</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="cbottom" <?php if($row->bottomright6 == 1){echo "checked='checked'";} ?> name="cbottomr6" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>5</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="cbottom" <?php if($row->bottomright5 == 1){echo "checked='checked'";} ?> name="cbottomr5" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>4</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="cbottom" <?php if($row->bottomright4 == 1){echo "checked='checked'";} ?> name="cbottomr4" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>3</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="cbottom" <?php if($row->bottomright3 == 1){echo "checked='checked'";} ?> name="cbottomr3" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>2</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="cbottom" <?php if($row->bottomright2 == 1){echo "checked='checked'";} ?> name="cbottomr2" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows" style="margin-left:10px">
                    <span class='toplabel'><strong>1</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="cbottom" <?php if($row->bottomright1 == 1){echo "checked='checked'";} ?> name="cbottomr1" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
            </td>
            
            <!--*****************************cbottom Left Teeth*****************************-->
            <td style="padding-ctop:5px;">
                <div class="checkbox-container inrows" style="margin-right:10px;">
                    <span class='toplabel'><strong>1</stron></span><br>
                    <label class="checkbox-labels">
                        <input type="checkbox" class="cbottom" <?php if($row->bottomleft1 == 1){echo "checked='checked'";} ?> name="cbottoml1" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>2</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="cbottom" <?php if($row->bottomleft2 == 1){echo "checked='checked'";} ?> name="cbottoml2" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>3</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="cbottom" <?php if($row->bottomleft3 == 1){echo "checked='checked'";} ?> name="cbottoml3" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>4</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="cbottom" <?php if($row->bottomleft4 == 1){echo "checked='checked'";} ?> name="cbottoml4" value="1"> 
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>5</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="cbottom" <?php if($row->bottomleft5 == 1){echo "checked='checked'";} ?> name="cbottoml5" value="1" >
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>6</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="cbottom" <?php if($row->bottomleft6 == 1){echo "checked='checked'";} ?> name="cbottoml6" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>7</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="cbottom" <?php if($row->bottomleft7 == 1){echo "checked='checked'";} ?> name="cbottoml7" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows">
                    <span class='toplabel'><strong>8</stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="cbottom" <?php if($row->bottomleft8 == 1){echo "checked='checked'";} ?> name="cbottoml8" value="1">
                        <span class="checkbox-custom rectangular"></span>
                    </label>
                </div>
                <div class="checkbox-container inrows" style="margin-left:0;">
                    <span><strong><?=$this->lang->line("all");?></stron></span><br> 
                    <label class="checkbox-labels">
                        <input type="checkbox" class="cbottomall" id="cbottomall" onclick="selectAll('cbottomall','cbottom');" name="cbottomlall" value="all">
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