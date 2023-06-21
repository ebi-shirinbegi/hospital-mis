<div class="row iRow">
  <div class="dashTitle">
    <?=$this->lang->line('hospital_system');?>
  </div>
  <div id="changeable">
      <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 iPanel">
        <div class="card text-white bg-primary mb-3 trnsition" style="max-width: 18rem;" onclick="javascript:bring_page('<?=base_url()?>index.php/queue/home','changeable')">
          <div class="card-header"><?=$this->lang->line('queue_part');?></div>
          <div class="card-body">
            <i class="fa fa-folder"></i>
          </div>
        </div>
      </div>

      <?php if($this->amc_auth->check_myrole('reception')){ ?>
      <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 iPanel">
        <div class="card text-white bg-primary mb-3 trnsition" style="max-width: 18rem;" onclick="javascript:bring_page('<?=base_url()?>index.php/register/home/register_list','changeable')">
          <div class="card-header"><?=$this->lang->line('register_part');?></div>
          <div class="card-body">
            <i class="fa fa-folder"></i>
          </div>
        </div>
      </div>
      <?php } ?>

      <?php if($this->amc_auth->check_myrole('drug_store')){ ?> 
      <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 iPanel">
        <div class="card text-white bg-primary mb-3 trnsition" style="max-width: 18rem;" onclick="javascript:bring_page('<?=base_url()?>index.php/drug_store/home','changeable')">
          <div class="card-header"><?=$this->lang->line('drug_store_part');?></div>
          <div class="card-body">
            <i class="fas fa-folder"></i>
          </div>
        </div>
      </div>
      <?php } ?> 

      <?php if($this->amc_auth->check_myrole('remains')){ ?> 
      <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 iPanel">
        <div class="card text-white bg-primary mb-3 trnsition" style="max-width: 18rem;" onclick="javascript:bring_page('<?=base_url()?>index.php/remains/home','changeable')">
          <div class="card-header"><?=$this->lang->line('remains_part');?></div>
          <div class="card-body">
            <i class="fa fa-folder"></i> 
          </div>
        </div>
      </div>
      <?php } ?>

      <?php if($this->amc_auth->check_myrole('expense')){ ?> 
      <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 iPanel">
        <div class="card text-white bg-primary mb-3 trnsition" style="max-width: 18rem;" onclick="javascript:bring_page('<?=base_url()?>index.php/expenses/home','changeable')">
          <div class="card-header"><?=$this->lang->line('expense_part');?></div>
          <div class="card-body">
            <i class="fa fa-folder"></i>
          </div>
        </div>
      </div>
      <?php } ?>

      <?php if($this->amc_auth->check_myrole('search')){ ?> 
      <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 iPanel">
        <div class="card text-white bg-primary mb-3 trnsition" style="max-width: 18rem;" onclick="javascript:bring_page('<?=base_url()?>index.php/xray/home','changeable')">
          <div class="card-header"><?=$this->lang->line('xray_part');?></div>
          <div class="card-body">
            <i class="fas fa-folder"></i>
          </div>
        </div>
      </div>
      <?php } ?>
      
      <?php if($this->amc_auth->check_myrole('xray_material')){ ?> 
      <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 iPanel">
        <div class="card text-white bg-primary mb-3 trnsition" style="max-width: 18rem;" onclick="javascript:bring_page('<?=base_url()?>index.php/xray/home/material_list','changeable')">
          <div class="card-header"><?=$this->lang->line('xray_material_part');?></div>
          <div class="card-body">
            <i class="fas fa-folder"></i>
          </div>
        </div>
      </div>
      <?php } ?>
      
      <?php if($this->amc_auth->check_myrole('report')){ ?> 
      <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 iPanel">
        <div class="card text-white bg-primary mb-3 trnsition" style="max-width: 18rem;" onclick="javascript:bring_page('<?=base_url()?>index.php/register/home/generalReport','changeable')">
          <div class="card-header"><?=$this->lang->line('general_report');?></div>
          <div class="card-body">
            <i class="fas fa-folder"></i>
          </div>
        </div>
      </div>
      <?php } ?>
  </div>
</div>