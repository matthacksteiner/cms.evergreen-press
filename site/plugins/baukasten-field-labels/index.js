window.panel.plugin("baukasten/layout-grid-values", {
	components: {
		"k-layout-column": {
			extends: "k-layout-column",
			mounted() {
				this.$el.dataset.width = this.width;
			},
		},
		"k-layout-selector": {
			extends: "k-layout-selector",
			mounted() {
				this.$nextTick(() => {
					document
						.querySelectorAll(
							".k-dialog.k-layout-selector .k-layout-selector-option .k-column"
						)
						.forEach((el) => {
							el.dataset.width =
								getComputedStyle(el).getPropertyValue("--width");
						});
				});
			},
		},
	},
});
