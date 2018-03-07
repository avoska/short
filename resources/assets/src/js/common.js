$(document).ready(function() {

	(function RomsMania() {

		(function menuToggling() {
			$('.js-menu-toggle').click(function() {
				$('.mobile-menu').toggleClass('is-open');
				$('.header__left').toggleClass('is-menu-open');
			});
		}());

		(function search() {
			var search = $('.js-search');
			var lastRequestId = 0;

			search.each(function() {
				var input = $(this).find('.js-input'),
					results = $(this).find('.js-search-results');

				results.hide();
				input.val(null);

				input.blur(function() {
					setTimeout(function() {
						results.hide();
						input.val(null);
					}, 500);
				});

				input.on('input change', function() {
					var $input = $(this);
					var query = $input.val().trim();

					setTimeout(function() {
						if(query.length && $input.val().trim() == query) {
							lastRequestId++;
							var requestId = lastRequestId;

							$.ajax({
								type: 'GET',
								dataType: 'html',
								url: '/search/autocomplete?name=' + encodeURIComponent(query)
							}).success(function(data) {
								if(requestId == lastRequestId) {
									results.html(data).show();
								}
							});
						}
					}, 500);
				});

			})
		}());

		(function filter() {
			var form = $('.filter'),
				results = form.find('.results'),
				searchStringInput = form.find('[name=name]'),
				searchStringOutput = $('.filter-query-title'),
				baseUri = form.attr('action'),
				request = getRequestParams(),
				page = request.page || 1,
				orderBy = request.orderBy || form.data('order') || 'name',
				orderAsc = request.orderAsc ||!form.data('desc'),
				lastRequestId = 0;

			initForm();
			initResultsTable();

			function getRequestParams() {
				var params = {};
				var parts = window.location.search.substr(1).split('&');
				for(var i = 0; parts[i] && i < parts.length; i++) {
					var p = parts[i].split('=');
					params[decodeURIComponent(p[0])] = decodeURIComponent(p[1] || '');
				}
				return params;
			}

			function initForm() {
				for(var name in request) {
					form.find('[name=' + name + ']').each(function(e) {
						$(this).val(request[name]);
					});
				}
				form.find('input').keyup(filterTable);
				form.find('select').change(filterTable);
				form.submit(function() {
					return false;
				});
			}

			function initResultsTable() {
				results.find('.sorter').click(function() {
					var field = $(this).data('field');
					orderAsc = field == orderBy ? !orderAsc : !$(this).data('desc');
					orderBy = field;
					filterTable();
				});

				results.find('[data-page]').click(function() {
					filterTable($(this).data('page'));
					return false;
				});

				window.scrollTo(0, 0);
			}

			function filterTable(page) {
				page = typeof page == 'number' ? page : 1;
				searchStringOutput.text(searchStringInput.val());

				var requestId = ++lastRequestId;
				setTimeout(function() {
					if(requestId == lastRequestId) {
						var query = '?' + form.serialize() + '&orderBy=' + orderBy + '&orderAsc=' + Number(orderAsc) + '&page=' + page;
						$.ajax({
							type: 'GET',
							dataType: 'html',
							url: baseUri + query
						}).success(function(data) {
							if(requestId == lastRequestId) {
								results.html(data);
								initResultsTable();
							}
						});
					}
				}, 100);
			}
		}());

		(function outSideClick() {
			var tooltip = $('.js-dropdown');
			var toggle = $('.js-dropdown-btn');

			(function dropDowns() {
				$('.js-dropdown-btn').click(function() {
					$(this).parent().find('.js-dropdown').toggleClass('is-visible');
				});
			}());

			$(document).click(function(ev) {
				if(!$(ev.target).closest(tooltip).length && !$(ev.target).is(tooltip)) {
					if(!$(ev.target).closest(toggle).length && !$(ev.target).is(toggle)) {
						if(tooltip.is(":visible")) {
							tooltip.removeClass('is-visible');
						}
					}
				}
			});
		}());

		(function rate() {
			var $rateit = $('.rateit');

			$rateit.bind('rated', function(event, value) {
				$rateit.rateit('readonly', true);
				$.get(window.location.href + '?rate=' + value);
			});

			if($rateit.data('rateit-readonly')) {
				$rateit.click(function() {
					alert('You already voted for this item.');
				})
			}
		}());

	}());
});