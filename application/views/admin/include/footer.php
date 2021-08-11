<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" id="admin_csrf" />
<input type="hidden" id="base_url" value="<?php echo $base_url; ?>">
</div>

<?php $page = $this->uri->segment(1); ?>

<!-- Jvector map JS -->
<!--<script src="<?php echo $base_url; ?>assets/plugins/jvectormap/jquery-jvectormap-2.0.3.min.js"></script>
<script src="<?php echo $base_url; ?>assets/plugins/jvectormap/jquery-jvectormap-world-mill.js"></script>-->

<script src="<?php echo $base_url; ?>assets/js/bootstrap-notify.min.js"></script>

<!-- Select2 JS -->
<script src="<?php echo $base_url; ?>assets/js/select2.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/sweetalert.min.js"></script>

<script  src="<?php echo $base_url; ?>assets/js/admin.js?v1.02"></script>

<input type="hidden" id="page" value="<?php echo $this->uri->segment(1); ?>">
<input type="hidden" id="provider_list_url" value="<?php echo site_url('provider_list') ?>">
<input type="hidden" id="requests_list_url" value="<?php echo site_url('request_list') ?>">
<input type="hidden" id="user_list_url" value="<?php echo site_url('users_list') ?>">
<input type="hidden" id="adminuser_list_url" value="<?php echo site_url('adminusers_list') ?>">

<?php if ($page == 'admin-profile')
{ ?>
	<script src="<?php echo $base_url; ?>assets/js/cropper_profile.js"></script>
	<script src="<?php echo $base_url; ?>assets/js/cropper.min.js"></script>
<?php
} ?>

<script src="<?php echo $base_url; ?>assets/js/jquery.checkboxall-1.0.min.js"></script>
<script src="<?php echo $base_url; ?>assets/js/admin_functions.js"></script>
<script src="<?php echo $base_url; ?>assets/js/users_subscription.js"></script>

<!--External js Start-->
<?php if ($this->uri->segment(1) == "reject-payment")
{ ?>
	<script src="<?php echo base_url(); ?>assets/js/edit_reject_booking_view.js"></script>
<?php
} ?>
<?php if ($this->uri->segment(2) == "emailsettings")
{ ?>
	<script src="<?php echo base_url(); ?>assets/js/admin_emailsettings.js"></script>
<?php
} ?>
<?php if ($this->uri->segment(2) == "stripe_payment_gateway")
{ ?>
	<script src="<?php echo base_url(); ?>assets/js/stripe_payment_gateway.js?v1.01"></script>
<?php
} ?>
<?php if ($this->uri->segment(2) == "paypal_payment_gateway")
{ ?>
  <script src="<?php echo base_url(); ?>assets/js/paypal_payment_gateway.js"></script>
<?php
} ?>
<?php if ($this->uri->segment(1) == "send-notification")
{ ?>
  <script src="<?php echo base_url(); ?>assets/js/send_notification.js"></script>
<?php
} ?>
<!--External js end-->

<?php
$page_id = $this->uri->segment(3);
$p = $this->uri->segment(2);
if ($page == 'user-login-history')
{ ?>
      <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/bootbox.min.js"></script>
    <?php
}
/*service list Active And De Active*/
if ($page == 'subcategories')
{
?>
      <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/bootbox.min.js"></script>
      <script type="text/javascript">
        var BASE_URL = $('#base_url').val();
        function delete_subcategories(val) {
            bootbox.confirm("Deleting sub-category will also delete its Services!! ", function (result) {
            if (result == true) {
              var url = BASE_URL + 'admin/categories/delete_subcategory';
              var keyname="<?php echo $this->security->get_csrf_token_name(); ?>";
              var keyvalue="<?php echo $this->security->get_csrf_hash(); ?>";
              var category_id = val;
              var data = { 
                category_id: category_id
              };
              data[keyname] = keyvalue;
              $.ajax({
                url: url,
                data: data,
                type: "POST",
                success: function (res) {
                  if (res == 1) {
                    $("#flash_success_message").show();
                    window.location = BASE_URL + 'subcategories';
                  } else {
                    window.location = BASE_URL + 'subcategories';
                  }
                }
              });
            }
          });
        }
        $(document).ready(function() {
          
          // $('.delete_subcategories').click(function () {
          //   var id = $(this).attr('data-id');
          //   delete_subcategories(id);
          // });
    
        });
      </script>
    <?php
}

if ($page == 'contact-details')
{ ?>
          <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/bootbox.min.js"></script>
          <script type="text/javascript">
            var BASE_URL = $('#base_url').val();
            function reply_contact(val,uname,umail) {
						 
              bootbox.confirm("<h4>REPLY CONTACT</h4><br><textarea id='replycont' class='form-control' placeholder='REPLY...' rows='10'></textarea> ", function (result) {
                if (result == true) {
					        var replycont=$("#replycont").val();
                  var url = BASE_URL + 'admin/contact/reply_contact';
                  var keyname="<?php echo $this->security->get_csrf_token_name(); ?>";
                  var keyvalue="<?php echo $this->security->get_csrf_hash(); ?>";
                  var contact_id = val;
				          var name = uname;
                  var email = umail;//alert(email);return false;
                  
                  var data = { 
                    contact_id: contact_id,
          					umail:umail,
          					uname:uname,
          					replycont:replycont
                  };
                  data[keyname] = keyvalue;
                  $.ajax({
                    url: url,
                    data: data,
                    type: "POST",
                    success: function (res) {
                      if (res == 1) {
                        $("#flash_success_message").show();
                        window.location = BASE_URL + 'contact-details/'+contact_id;
                      } else {
                        window.location = BASE_URL + 'contact-details/'+contact_id;
                      }
                    }
                  });
                }
              });
            }
            $(document).ready(function() {

              $('.reply_contact').on('click', function () {
                var id = $(this).attr('data-id');
                var umail = $(this).attr('data-mail');
                var uname = $(this).attr('data-uname');
                reply_contact(id,uname,umail);
              });
                  
            });
          </script>
      <?php
}

if ($page == 'categories' || $page == 'user-subscription')
{
?>
                <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/bootbox.min.js"></script>
                <script type="text/javascript">
                  var BASE_URL = $('#base_url').val();
                  function delete_categories(val) {
                    bootbox.confirm("Deleting category will also delete its sub-categories and Services!! ", function (result) {
                      if (result == true) {
                        var url = BASE_URL + 'admin/categories/delete_category';
                        var keyname="<?php echo $this->security->get_csrf_token_name(); ?>";
                        var keyvalue="<?php echo $this->security->get_csrf_hash(); ?>";
                        var category_id = val;
                        var data = { 
                          category_id: category_id
                        };
                      data[keyname] = keyvalue;
                      $.ajax({
                        url: url,
                        data: data,
                        type: "POST",
                        success: function (res) {
                          if (res == 1) {
                            $("#flash_success_message").show();
                            window.location = BASE_URL + 'categories';
                          } else {
                            window.location = BASE_URL + 'categories';
                          }
                        }
                      });
                    }
                  });
                }
                $(document).ready(function() {
                  // $('.delete_categories').on('click', function () {
                  //   var id = $(this).attr('data-id');
                  //   delete_categories(id);
                  // });
                });
                  </script>
                  <?php
}
if ($p == 'country_code_config')
{
?>
      <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/bootbox.min.js"></script>
       <script type="text/javascript">
       var BASE_URL = $('#base_url').val();
         function delete_country_code_config(val) {
  bootbox.confirm("Are you sure want to Delete ? ", function (result) {
    if (result == true) {
      var url = BASE_URL + 'admin/country_code_config/delete_country_code_config';
      var keyname="<?php echo $this->security->get_csrf_token_name(); ?>";
      var keyvalue="<?php echo $this->security->get_csrf_hash(); ?>";
      var tbl_id = val;
      var data = { 
          tbl_id: tbl_id
};
data[keyname] = keyvalue;
      $.ajax({
        url: url,
        data: data,
        type: "POST",
        success: function (res) {
          if (res == 1) {
            window.location = BASE_URL + 'admin/country_code_config';
          } else {
            window.location = BASE_URL + 'admin/country_code_config';
          }
        }
      });
    }
  });
}
       $(document).ready(function() {
          $('.delete_country_code_config').on('click', function () {
      var id = $(this).attr('data-id');
      delete_country_code_config(id);
    });
    
       });
      </script>
      <?php
}
if ($p == 'footer_menu')
{
?>
        <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/bootbox.min.js"></script>
         <script type="text/javascript">
         var BASE_URL = $('#base_url').val();
           function delete_footer_menu(val) {
    bootbox.confirm("Are you sure want to Delete ? ", function (result) {
      if (result == true) {
        var url = BASE_URL + 'admin/footer_menu/delete_footer_menu';
        var keyname="<?php echo $this->security->get_csrf_token_name(); ?>";
        var keyvalue="<?php echo $this->security->get_csrf_hash(); ?>";
        var tbl_id = val;
        var data = { 
            tbl_id: tbl_id
};
data[keyname] = keyvalue;
        $.ajax({
          url: url,
          data: data,
          type: "POST",
          success: function (res) {
            if (res == 1) {
              window.location = BASE_URL + 'admin/footer_menu';
            } else {
              window.location = BASE_URL + 'admin/footer_menu';
            }
          }
        });
      }
    });
  }
         $(document).ready(function() {
            $('.delete_footer_menu').on('click', function () {
        var id = $(this).attr('data-id');
        delete_footer_menu(id);
      });
      
         });
        </script>
        <?php
}
if ($p == 'footer_submenu')
{
?>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/bootbox.min.js"></script>
     <script type="text/javascript">
     var BASE_URL = $('#base_url').val();
       function delete_footer_submenu(val) {
        console.log("1");
    bootbox.confirm("Are you sure want to Delete ? ", function (result) {
      if (result == true) {
        var url = BASE_URL + 'admin/footer_submenu/delete_footer_submenu';
        var keyname="<?php echo $this->security->get_csrf_token_name(); ?>";
        var keyvalue="<?php echo $this->security->get_csrf_hash(); ?>";
        var tbl_id = val;
        var data = { 
            tbl_id: tbl_id
};
data[keyname] = keyvalue;
        $.ajax({
          url: url,
          data: data,
          type: "POST",
          success: function (res) {
            if (res == 1) {
              window.location = BASE_URL + 'admin/footer_submenu';
            } else {
              window.location = BASE_URL + 'admin/footer_submenu';
            }
          }
        });
      }
    });
  }
     $(document).ready(function() {
  $('.delete_footer_submenu').on('click', function () {
    console.log("2");
    var id = $(this).attr('data-id');
    delete_footer_submenu(id);
  });
		$('#menu_status').click(function() {
			$('#sub_menu').attr('required', 'required');
			$('.sub_menu').show();
		  });           
		  $('#menu_status_one').click(function() {
			$('#sub_menu').removeAttr('required', 'required');
			$('.sub_menu').hide();
		  });
     });
    </script>
    <?php
}
if ($p == 'whitelabel') {
?>
  <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/bootbox.min.js"></script>
  <script type="text/javascript">
    var BASE_URL = $('#base_url').val();

    function delete_whitelabel(val) {
      bootbox.confirm("Are you sure want to delete this whitelabel ? ", function(result) {
        if (result == true) {
          var url = BASE_URL + 'admin/whitelabel/delete_whitelabel';
          var keyname = "<?php echo $this->security->get_csrf_token_name(); ?>";
          var keyvalue = "<?php echo $this->security->get_csrf_hash(); ?>";
          var data = {
            whitelabel_id: val
          };
          data[keyname] = keyvalue;
          $.ajax({
            url: url,
            data: data,
            type: "POST",
            success: function(res) {
              var data = JSON.parse(res)
              if (data.sec == 1) {
                window.location = BASE_URL + 'admin/whitelabel';
              } else {
                alert('Something wrong, Please try again')
              }
            }
          });
        }
      });
    }

    $(document).ready(function() {

      $('#category_select').select2({
        placeholder: "Select a category",
      });
      $('#subcategory_select').select2({
        placeholder: "Select a subcategory",
      });

      $('#category_select').on('change', function(){
        var url = BASE_URL + 'admin/whitelabel/change_subcategory';
        var keyname = "<?php echo $this->security->get_csrf_token_name(); ?>";
        var keyvalue = "<?php echo $this->security->get_csrf_hash(); ?>";
        var categories = $(this).val();
        var subcategories = $('#subcategory_select').val();
        var data = {
          categories: categories,
          subcategories : subcategories
        };
        data[keyname] = keyvalue;
        $.ajax({
          url: url,
          data: data,
          type: "POST",
          success: function(res) {
            $('#subcategory_select').html(res);
            $('#subcategory_select').trigger('change')
            
          }
        });
      });

      $('.delete_whitelabel').on('click', function() {
        var id = $(this).attr('data-id');
        delete_whitelabel(id);
      });

      $('#logo_openfile').on('click', function() {
        $('#logo_file').click();
      });
      $('#fav_openfile').on('click', function() {
        $('#favicon_file').click();
      });

      $('.logofile_upload').on('change', function() {
        var url = BASE_URL + 'admin/whitelabel/upload_logofile';
        var keyname = "<?php echo $this->security->get_csrf_token_name(); ?>";
        var keyvalue = "<?php echo $this->security->get_csrf_hash(); ?>";
        var file_data = $(this).prop("files")[0];
        var form_data = new FormData();
        form_data.append("logofile", file_data);
        var type = $(this).attr("id");
        form_data.append(keyname, keyvalue)
        console.log(form_data)
        $.ajax({
          type: 'POST',
          url: url,
          enctype: 'multipart/form-data',
          data: form_data,
          cache: false,
          contentType: false,
          dataType: 'json',
          processData: false,
          success: function(res) {
            if (res.sec == 1) {
              if (type == "favicon_file") {
                $('.fav-icon').attr('src', BASE_URL + "assets/wll_logos/temp/" + res.name);
                $('#crop_favicon_img').val(res.name);
              }
              if (type == "logo_file") {
                $('.site-logo').attr('src', BASE_URL + "assets/wll_logos/temp/" + res.name);
                $('#crop_logo_img').val(res.name);
              }
            } else {
              alert(res.message)
            }
          }
        });
      });

      $('#eidt_whitelabel').bootstrapValidator({
        fields: {
          name: {
            validators: {
              notEmpty: {
                message: 'Please add name'
              }
            }
          },
        }
      }).on('success.form.bv', function(e) {
        return true;
      });
    });
  </script>

<?php
}
if ($p == 'blog')
{
?>
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/bootbox.min.js"></script>
    <script type="text/javascript">
    var BASE_URL = $('#base_url').val();

    function delete_blog(val) {
      bootbox.confirm("Are you sure want to Delete ? ", function(result) {
        if(result == true) {
          var url = BASE_URL + 'admin/blog/delete';
          var keyname = "<?php echo $this->security->get_csrf_token_name(); ?>";
          var keyvalue = "<?php echo $this->security->get_csrf_hash(); ?>";
          var tbl_id = val;
          var data = {
            tbl_id: tbl_id
          };
          data[keyname] = keyvalue;
          $.ajax({
            url: url,
            data: data,
            type: "POST",
            success: function(res) {
              if(res == 1) {
                window.location = BASE_URL + 'admin/blog';
              } else {
                window.location = BASE_URL + 'admin/blog';
              }
            }
          });
        }
      });
    }
    $(document).ready(function() {
      $('.delete_blog').on('click', function() {
        var id = $(this).attr('data-id');
        delete_blog(id);
      });
    });
    </script>
    <?php
}
if ($page == 'add-wep-keyword')
{
?>
    <script type="text/javascript">
    $(document).ready(function() {
      $(".check_key_name").keypress(function(event) {
        var inputValue = event.which;
        if((!(inputValue >= 65 && inputValue <= 90) && !(inputValue >= 97 && inputValue <= 120)) && inputValue != 95) {
          event.preventDefault();
        }
      });
    });
    </script>
<?php
}

if ($page == 'wep_language')
{ ?>
    <script type="text/javascript">
    $(document).ready(function() {
      var csrf_token = $('#web_csrf').val();
      language_table = $('#language_web_table').DataTable({
        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "order": [], //Initial no order.
        "ajax": {
          "url": '<?php echo base_url('
          admin / language / language_web_list '); ?>',
          "type": "POST",
          "data": function(data) {
            data.csrf_token_name = $('#web_csrf').val();
          }
        },
        "columnDefs": [{
          "targets": [], //first column / numbering column
          "orderable": false, //set not orderable
        }, ],
      });
    });
    </script>
    <?php
} ?>
   
    <?php
$page_id = $this->uri->segment(2);
if ($page == 'app_page_list')
{ ?>
        <script type="text/javascript">
          $(document).ready(function() {
            var csrf_token=$('#app_csrf').val();

            language_table = $('#language_app_table').DataTable({
              "processing": true, //Feature control the processing indicator.
              "serverSide": true, //Feature control DataTables' server-side processing mode.
              "order": [], //Initial no order.
              "ajax": {
                "url": '<?php echo base_url('admin/language/language_list'); ?>',  
                "type": "POST",
                "data":function(data)
                {
                  data.csrf_token_name =$('#app_csrf').val();
                  data.page_key = "<?php echo $page_id ?>";
                }
              },
              "columnDefs": [
                {
                    "targets": [  ], //first column / numbering column
                    "orderable": false, //set not orderable
                },
              ],
            });
          });
        </script>
    <?php
} ?>
    <script type="text/javascript">
      $(document).ready(function() {
        var activeItem = $("#sidebar-menu li.active");
        if (activeItem.length > 0) {
          $("#sidebar-menu").parent().scrollTop(activeItem[0].offsetTop);
        }
      });
    </script>  
    
</body>
</html>
