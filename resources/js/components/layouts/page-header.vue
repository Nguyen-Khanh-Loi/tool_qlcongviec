<script>
export default {
    components: {},
    props: {
        title: {
            type: String,
            default: '',
        },
        code: {
            type: Number,
            default: '',
        },
        items: {
            type: Array,
            default: () => {
                return []
            },
        },
    },
}
</script>

<template>
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between pb-2">
                <h4 class="mb-0">{{ `${code ? '['+code+'] '+title : title}` }}</h4>
                <div class="page-title-right">
                    <b-breadcrumb :items="items" class="m-0"></b-breadcrumb>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->
</template>
<style lang="scss">
    #projectTable_paginate{
            .paginate_button{
                padding: 0 10px !important;
                border: none;
                cursor: pointer;
             }
             .active{
                padding: 0 !important;
                a{
                    background: #ffa000;
                    border:#ffa000;
                    height: 100%;
                }
             }
        }
       
</style>
