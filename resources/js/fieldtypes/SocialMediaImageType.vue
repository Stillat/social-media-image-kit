<script>
const ExtendedComponent = Vue.extend({
    extends: Vue.options.components['select-fieldtype'],
    created() {
        this.config.options = this.meta.sizes;
    }
});

export default ExtendedComponent;
</script>