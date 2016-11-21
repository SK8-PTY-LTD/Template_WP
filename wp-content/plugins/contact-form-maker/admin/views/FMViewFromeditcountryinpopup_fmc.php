<?php

class FMViewFromeditcountryinpopup_fmc {
  ////////////////////////////////////////////////////////////////////////////////////////
  // Events                                                                             //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Constants                                                                          //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Variables                                                                          //
  ////////////////////////////////////////////////////////////////////////////////////////
  private $model;


  ////////////////////////////////////////////////////////////////////////////////////////
  // Constructor & Destructor                                                           //
  ////////////////////////////////////////////////////////////////////////////////////////
  public function __construct($model) {
    $this->model = $model;
  }
  ////////////////////////////////////////////////////////////////////////////////////////
  // Public Methods                                                                     //
  ////////////////////////////////////////////////////////////////////////////////////////
	public function display() {
		$id = ((isset($_GET['field_id'])) ? esc_html(stripslashes($_GET['field_id'])) : 0);
		wp_print_scripts('jquery');
		wp_print_scripts('jquery-ui-core');
		wp_print_scripts('jquery-ui-widget');
		wp_print_scripts('jquery-ui-mouse');
		wp_print_scripts('jquery-ui-slider');
		wp_print_scripts('jquery-ui-sortable');
		
		?>
		<style>
		.country-list {
			padding:10px 0;
		}
		
		.country-list ul {
			font-family: Segoe UI !important;
			font-size:13px;
		}
		.country-list > div {
			display:inline-block;
		}
		
		.save-cancel {
			float:right;
		}
		
		.fm-select-remove {
			background: #4EC0D9;
			width: 78px;
			height: 32px;
			border: 1px solid #4EC0D9;
			border-radius: 6px;
			color: #fff;
			cursor:pointer;
		}
		
		.fm-select-remove.large {
			width: 90px;
		}
		</style>
		<div class="country-list">
			<div class="select-remove">
				<button class="fm-select-remove large" onclick="select_all(); return false;">
					Select all
					<span></span>
				</button>
				<button class="fm-select-remove large" onclick="remove_all(); return false;">
					Remove all
					<span></span>
				</button>
			</div>
			<div class="save-cancel">
				<button class="fm-select-remove" onclick="save_list(); return false;">
					Save
					<span></span>
				</button>
			</div>
			<ul id="countries_list" style="list-style: none; padding: 0px;"></ul>
		</div>	
		<script>
			selec_coutries = [];
			coutries = ["", "Afghanistan", "Albania", "Algeria", "Andorra", "Angola", "Antigua and Barbuda", "Argentina", "Armenia", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bhutan", "Bolivia", "Bosnia and Herzegovina", "Botswana", "Brazil", "Brunei", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Central African Republic", "Chad", "Chile", "China", "Colombia", "Comoros", "Congo (Brazzaville)", "Congo", "Costa Rica", "Cote d'Ivoire", "Croatia", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "East Timor (Timor Timur)", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Fiji", "Finland", "France", "Gabon", "Gambia, The", "Georgia", "Germany", "Ghana", "Greece", "Grenada", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Honduras", "Hungary", "Iceland", "India", "Indonesia", "Iran", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, North", "Korea, South", "Kuwait", "Kyrgyzstan", "Laos", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libya", "Liechtenstein", "Lithuania", "Luxembourg", "Macedonia", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Mauritania", "Mauritius", "Mexico", "Micronesia", "Moldova", "Monaco", "Mongolia", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Norway", "Oman", "Pakistan", "Palau", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Poland", "Portugal", "Qatar", "Romania", "Russia", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Serbia and Montenegro", "Seychelles", "Sierra Leone", "Singapore", "Slovakia", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "Spain", "Sri Lanka", "Sudan", "Suriname", "Swaziland", "Sweden", "Switzerland", "Syria", "Taiwan", "Tajikistan", "Tanzania", "Thailand", "Togo", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "Uruguay", "Uzbekistan", "Vanuatu", "Vatican City", "Venezuela", "Vietnam", "Yemen", "Zambia", "Zimbabwe"];
			select_ = window.parent.document.getElementById('<?php echo $id ?>_elementform_id_temp');
			n = select_.childNodes.length;
			for (i = 0; i < n; i++) {
				selec_coutries.push(select_.childNodes[i].value);
				var ch = document.createElement('input');
					ch.setAttribute("type", "checkbox");
					ch.setAttribute("checked", "checked");
					ch.value = select_.childNodes[i].value;
					ch.id = i + "ch";

				var p = document.createElement('span');
					p.style.cssText = "color:#000000; font-size: 13px; cursor:move";
					p.innerHTML = select_.childNodes[i].value;
				var li = document.createElement('li');
					li.style.cssText = "margin:3px; vertical-align:middle";
					li.id = i;
					li.appendChild(ch);
					li.appendChild(p);
				document.getElementById('countries_list').appendChild(li);
			}
			cur = i;
			m = coutries.length;
			for (i = 0; i < m; i++) {
				isin = isValueInArray(selec_coutries, coutries[i]);
				if (!isin) {
					var ch = document.createElement('input');
						ch.setAttribute("type", "checkbox");
						ch.value = coutries[i];
						ch.id = cur + "ch";

					var p = document.createElement('span');
						p.style.cssText = "color:#000000; font-size: 13px; cursor:move";
						p.innerHTML = coutries[i];
					var li = document.createElement('li');
						li.style.cssText = "margin:3px; vertical-align:middle";
						li.id = cur;
						li.appendChild(ch);
						li.appendChild(p);
					document.getElementById('countries_list').appendChild(li);
					cur++;
				}
			}
			jQuery(function () {
				jQuery("#countries_list").sortable();
				jQuery("#countries_list").disableSelection();
			});

			function isValueInArray(arr, val) {
				inArray = false;
				for (x = 0; x < arr.length; x++) {
					if (val == arr[x]) {
						inArray = true;
					}
				}
				return inArray;
			}
			function save_list() {
				select_.innerHTML = ""
				ul = document.getElementById('countries_list');
				n = ul.childNodes.length;
				for (i = 0; i < n; i++) {
					if (ul.childNodes[i].tagName == "LI") {
						id = ul.childNodes[i].id;
						if (document.getElementById(id + 'ch').checked) {
							var option_ = document.createElement('option');
								option_.setAttribute("value", document.getElementById(id + 'ch').value);
								option_.innerHTML = document.getElementById(id + 'ch').value;
							select_.appendChild(option_);
						}
					}
				}
				window.parent.tb_remove();
			}
			function select_all() {
				for (i = 0; i < 194; i++) {
					document.getElementById(i + 'ch').checked = true;
				}
			}
			function remove_all() {
				for (i = 0; i < 194; i++) {
					document.getElementById(i + 'ch').checked = false;
				}
			}
		</script>
		<?php
		die();
	}

  ////////////////////////////////////////////////////////////////////////////////////////
  // Getters & Setters                                                                  //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Private Methods                                                                    //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Listeners                                                                          //
  ////////////////////////////////////////////////////////////////////////////////////////
}