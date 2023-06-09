<div class="row">
	<div class="col-xs-12">
		<!-- PAGE CONTENT BEGINS -->
		<div class="form-horizontal">
			<form onsubmit="updateCategory(event)">
				<div class="form-group">
					<label class="col-md-3 control-label no-padding-right" for="form-field-1"> Category Name </label>
					<label class="col-md-1 control-label no-padding-right">:</label>
					<div class="col-md-3">
						<input type="text" id="catname" name="catname" placeholder="Category Name" value="<?php echo $selected->ProductCategory_Name; ?>" class="form-control" />
						<input name="id" id="id" type="hidden" value="<?php echo $selected->ProductCategory_SlNo; ?>" />
						<span id="msg"></span>
						<span style="color:red;font-size:15px;">
					</div>
				</div>

				<div class="form-group">
					<label class="col-md-3 control-label no-padding-right" for="form-field-1"> Discount </label>
					<label class="col-md-1 control-label no-padding-right">:</label>
					<div class="col-md-3">
						<input type="number" step="0.01" min="0" id="discount" name="discount" placeholder="Discount" value="<?php echo $selected->Discount; ?>" class="form-control" />
						<span id="msg"></span>
						<?php echo form_error('discount'); ?>
						<span style="color:red;font-size:15px;">
					</div>
				</div>

				<div class="form-group">
					<label class="col-md-3 control-label no-padding-right" for="description">Description </label>
					<label class="col-md-1 control-label no-padding-right">:</label>
					<div class="col-md-3">
						<textarea class="form-control" name="catdescrip" id="catdescrip"><?php echo $selected->ProductCategory_Description; ?></textarea>
					</div>
				</div>

				<div class="form-group">
					<label class="col-md-3 control-label no-padding-right" for="form-field-1"></label>
					<label class="col-md-1 control-label no-padding-right"></label>
					<div class="col-md-3">
						<button type="submit" class="btn btn-sm btn-success" name="btnSubmit">
							Submit
							<i class="ace-icon fa fa-arrow-right icon-on-right bigger-110"></i>
						</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>



<div class="row">
	<div class="col-xs-12">
		<div class="clearfix">
			<div class="pull-right tableTools-container"></div>
		</div>
		<div class="table-header">
			Category Information
		</div>

		<!-- div.table-responsive -->

		<!-- div.dataTables_borderWrap -->
		<div id="saveResult">
			<table id="dynamic-table" class="table table-striped table-bordered table-hover">
				<thead>
					<tr>
						<th class="center" style="display:none;">
							<label class="pos-rel">
								<input type="checkbox" class="ace" />
								<span class="lbl"></span>
							</label>
						</th>
						<th>SL No</th>
						<th>Category Name</th>
						<th>Category Discoutn</th>
						<th class="hidden-480">Description</th>

						<th>Action</th>
					</tr>
				</thead>

				<tbody>
					<?php
					$BRANCHid = $this->session->userdata('BRANCHid');
					$query = $this->db->query("SELECT * FROM tbl_productcategory where status='a' AND category_branchid = '$BRANCHid' order by ProductCategory_Name asc");
					$row = $query->result();
					//while($row as $row){ 
					?>
					<?php $i = 1;
					foreach ($row as $row) { ?>
						<tr>
							<td class="center" style="display:none;">
								<label class="pos-rel">
									<input type="checkbox" class="ace" />
									<span class="lbl"></span>
								</label>
							</td>

							<td><?php echo $i++; ?></td>
							<td><a href="#"><?php echo $row->ProductCategory_Name; ?></a></td>
							<td><a href="#"><?php echo $row->Discount; ?> %</a></td>
							<td class="hidden-480"><?php echo $row->ProductCategory_Description; ?></td>
							<td>
								<div class="hidden-sm hidden-xs action-buttons">
									<a class="blue" href="#">
										<i class="ace-icon fa fa-search-plus bigger-130"></i>
									</a>

									<a class="green" href="<?php echo base_url() ?>Administrator/page/catedit/<?php echo $row->ProductCategory_SlNo; ?>" title="Eidt" onclick="return confirm('Are you sure you want to Edit this item?');">
										<i class="ace-icon fa fa-pencil bigger-130"></i>
									</a>

									<a class="red" href="#" onclick="deleted(<?php echo $row->ProductCategory_SlNo; ?>)">
										<i class="ace-icon fa fa-trash-o bigger-130"></i>
									</a>
								</div>
							</td>
						</tr>

					<?php } ?>
				</tbody>
			</table>
		</div>
		<!-- PAGE CONTENT ENDS -->
	</div><!-- /.col -->
</div><!-- /.row -->

<script type="text/javascript">
	function updateCategory(event) {
		event.preventDefault();
		var catname = $("#catname").val();
		var catdescrip = $("#catdescrip").val();
		var id = $("#id").val();
		if (catname == "") {
			$("#msg").html("Required Filed").css("color", "red");
			return false;
		}
		var urldata = "<?php echo base_url(); ?>Administrator/page/catupdate";
		var formdata = new FormData(event.target)
		formdata.append("id", id);
		$.ajax({
			type: "POST",
			url: urldata,
			dataType: 'JSON',
			data: formdata,
			processData: false,
			contentType: false,
			success: data => {
				alert("Category update successfully");
				setTimeout(() => {
					location.href = "/category"
				}, 500)
			}
		});
	}
</script>
<script type="text/javascript">
	function deleted(id) {
		var deletedd = id;
		var inputdata = 'deleted=' + deletedd;
		//alert(inputdata);
		var urldata = "<?php echo base_url(); ?>Administrator/page/catdelete";
		$.ajax({
			type: "POST",
			url: urldata,
			data: inputdata,
			success: function(data) {
				alert("data");
				location.href = '<?php echo base_url(); ?>category';
			}
		});
	}
</script>