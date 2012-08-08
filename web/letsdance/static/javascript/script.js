$(document).ready(function(){
		$('#id').click(function(){
			$.ajax({
			url: "{{ path('obr') }}",
			dataType: "html",
			success: function(data){
				$('#id').html(data);
			}
		});
			//show();
		})
	});
