panel.plugin("baukasten/field-methods", {
	fields: {
		linkObject: {
			extends: "k-object-field",
		},
	},
	components: {
		"k-object-field-preview": {
			props: {
				value: String,
				column: Object,
				field: Object,
			},
			computed: {
				preview() {
					if (!this.value) {
						return null;
					}

					let preview = "";

					if (this.value.link) {
						if (this.value.link.startsWith("http")) {
							preview += "🌐 ";
						} else if (this.value.link.startsWith("page://")) {
							preview += "📄 ";
						} else if (this.value.link.startsWith("file://")) {
							preview += "📎 ";
						} else if (this.value.link.startsWith("tel:")) {
							preview += "📞 ";
						} else if (this.value.link.startsWith("mailto:")) {
							preview += "✉️ ";
						}
					}

					if (this.value.linktext) {
						preview += this.value.linktext;
					} else if (this.value.link && this.value.link.startsWith("page://")) {
						preview += this.value.pageTitle
							? this.value.pageTitle
							: this.value.link;
					} else if (this.value.link) {
						preview += this.value.link;
					}

					if (this.value.target) {
						preview += " ↗️";
					}

					if (this.value.anchortoggle && this.value.anchor) {
						preview += ` #${this.value.anchor}`;
					}

					return preview;
				},
			},
			template: `
        <div class="k-object-field-preview">
          {{ preview }}
        </div>
      `,
		},
	},
});
