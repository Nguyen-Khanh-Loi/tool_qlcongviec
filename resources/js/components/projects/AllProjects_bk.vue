<script>
export default {
    data() {
        return {
            task: {},
            loading: false,
        };
    },
    methods: {     
        createNewTask() {
            this.loading = true;
            axios
                .post('/api/tasks/create', this.task)
                .then(response => {
                    //console.log(response);
                    this.product = response.data;
                    this.loading = false;
                })
                .catch(error => {
                    console.log(error);
                });
        },
    },
    created: () => {
        document.body.removeAttribute("data-layout", "horizontal");
        document.body.removeAttribute("data-topbar", "dark");
        document.body.setAttribute("data-sidebar", "dark");
    },
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
    <div class="container-fluid p-0">
        <div class="row no-gutters">
            <div class="col-lg-12">
                <div class="authentication-page-content p-4 d-flex align-items-center min-vh-100">
                    <div class="w-100">
                        <div class="row justify-content-center">
                            <div class="col-lg-12">
                                <div>
                                    <div class="text-center">
                                        <h1>demo</h1>
                                        <h4 class="font-size-18 mt-4">Create task</h4>
                                    </div>
                                    <div class="p-2 mt-5">
                                        <form class="form-horizontal" @submit.prevent="createNewTask">
                                            <div class="form-group auth-form-group-custom mb-4">
                                                <i class="ri-user-2-line auti-custom-input-icon"></i>
                                                <label for="fname">Name Task</label>
                                                <input v-model="task.title" type="text" class="form-control"
                                                    id="title"
                                                    placeholder="Name task" />
                                            </div>                                                           
                                            <div class="text-center">
                                                <button class="btn btn-primary w-md waves-effect waves-light"
                                                    type="submit">Create</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- container-fluid -->
</template>