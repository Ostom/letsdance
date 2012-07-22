$(document).ready(function() {

			var button = $('#uploadButton'), interval;

			$.ajax_upload(button, {
						action : 'upload.php',
						name : 'myfile',
						onSubmit : function(file, ext) {
							// показываем картинку загрузки файла
							$("img#load").attr("src", "load.gif");
							$("#uploadButton font").text('Загрузка');

							/*
							 * Выключаем кнопку на время загрузки файла
							 */
							this.disable();

						},
						onComplete : function(file, response) {
							// убираем картинку загрузки файла
							$("img#load").attr("src", "loadstop.gif");
							$("#uploadButton font").text('Загрузить');

							// снова включаем кнопку
							this.enable();

							// показываем что файл загружен
							$("<li>" + file + "</li>").appendTo("#files");

						}
					});
		});
