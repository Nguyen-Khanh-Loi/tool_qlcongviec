<script>
import Index from "../projects/Index.vue";
export default {
    components: {
        Index
    },
    data() {
        return {
            task: {},
            loading: false,
        };
    },
    methods: {     
        // createNewTask() {
        //     this.loading = true;
        //     axios
        //         .post('/api/tasks/create', this.task)
        //         .then(response => {
        //             //console.log(response);
        //             this.product = response.data;
        //             this.loading = false;
        //         })
        //         .catch(error => {
        //             console.log(error);
        //         });
        // },
    },
    // created: () => {
    //     document.body.removeAttribute("data-layout", "horizontal");
    //     document.body.removeAttribute("data-topbar", "dark");
    //     document.body.setAttribute("data-sidebar", "dark");
    // },
    mounted() {
        document.body.classList.remove("auth-body-bg");
        if (this.loader === true) {
        document.getElementById("preloader").style.display = "block";
        document.getElementById("status").style.display = "block";

        setTimeout(function () {
            document.getElementById("preloader").style.display = "none";
            document.getElementById("status").style.display = "none";
        }, 2500);
        } else {
            document.getElementById("preloader").style.display = "none";
            document.getElementById("status").style.display = "none";
        }
    },
};
</script>
<template>
    <Index />
</template>
