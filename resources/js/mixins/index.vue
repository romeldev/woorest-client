<script>
export default {
    methods: {
        $toast(message, type=null, timeout=3000) {
            switch(type) {
                case 'success': 
                    this.$toastr.s(message);
                    break
                case 'warning': 
                    this.$toastr.w(message);
                    break
                case 'error': 
                    this.$toastr.e(message);
                    break
                case 'danger': 
                    this.$toastr.e(message);
                    break
                case 'info': 
                    this.$toastr.i(message);
                    break
            }
        },
    },
};
</script>
