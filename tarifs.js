   
   $(document).ready(function()
   {
      $("#RadioButton1").change(function()
      {
         if ($('#RadioButton1').is(':checked'))
         {
            $('#Editbox1').val(100);
         }
      });
      $("#RadioButton1").trigger('change');
      $("#RadioButton2").change(function()
      {
         if ($('#RadioButton2').is(':checked'))
         {
            $('#Editbox1').val(95 * 2);
         }
      });
      $("#RadioButton2").trigger('change');
      $("#RadioButton3").change(function()
      {
         if ($('#RadioButton3').is(':checked'))
         {
            $('#Editbox1').val(90 * 3);
         }
      });
      $("#RadioButton3").trigger('change');
      $("#RadioButton4").change(function()
      {
         if ($('#RadioButton4').is(':checked'))
         {
            $('#Editbox1').val(85 * 4);
         }
      });
      $("#RadioButton4").trigger('change');
      $('#headerMenu .dropdown-toggle').dropdown({popperConfig:{placement:'bottom-start',modifiers:{computeStyle:{gpuAcceleration:false}}}});
      $(document).on('click','.headerMenu-navbar-collapse.show',function(e)
      {
         if ($(e.target).is('a') && ($(e.target).attr('class') != 'dropdown-toggle')) 
         {
            $(this).collapse('hide');
         }
      });
      function skrollrInit()
      {
         skrollr.init({forceHeight: false, mobileCheck: function() { return false; }, smoothScrolling: false});
      }
      skrollrInit();
      $("#RadioButton5").change(function()
      {
         if ($('#RadioButton5').is(':checked'))
         {
            $('#Editbox1').val(80 * 5);
         }
      });
      $("#RadioButton5").trigger('change');
      $("#RadioButton6").change(function()
      {
         if ($('#RadioButton6').is(':checked'))
         {
            $('#Editbox1').val(75 * 6);
         }
      });
      $("#RadioButton6").trigger('change');
   });
