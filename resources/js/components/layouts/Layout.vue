<script>
import Header from "./Header.vue";
import Footer from "./Footer.vue";
import SideBar from "./SiderBar.vue";
export default {
    components:{
        Header,
        Footer,
        SideBar,
    },

    data() { 

        return{
            listRecentlyViewed: [
            {
                image: './image/red.jpg',
                title:'PROJECT1'
            }, {
                image: './image/red.jpg',
                title:'PROJECT2'
            }, {
                image: './image/red.jpg',
                title:'PROJECT3'
            },  {
                image: './image/red.jpg',
                title:'PROJECT4'
            },
        ],

         listWorkingSpaceContainer: [
            {
                image: './image/red.jpg',
                title:'PROJECT1'

            }, {
                image: './image/red.jpg',
                title:'PROJECT2'
            }, {
                image: './image/red.jpg',
                title:'PROJECT3'
            }, 
        ],
        };
       
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
        };
    },
};
</script>

<template>
    <div>
        <div id="preloader">
            <div id="status">
                <div class="spinner">
                    <i class="ri-loader-line spin-icon"></i>
                </div>
            </div>
        </div>
        <!-- Begin page -->
        <div id="layout-wrapper">
            <Header />
            <SideBar />
            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->
            <div class="main-content">
                <div class="page-content">
                    <!--  -->
                    <div class="recently-viewed">
                        <div class="recently-viewed-title">
                            <i class="ri-time-line"></i>
                             <p>Đã xem gần đây</p>
                        </div>

                        <div class="recently-viewed-content">
                            <ul class="list_item">
                                <li class="item" v-for="(item,index) in listRecentlyViewed" :key="index">
                                    <a href="">
                                        <p>{{item.title}}</p>
                                    </a>
                                    <div class="item-icon">
                                        <div class="icon-active" title="Có hoạt động mới trong bảng này."></div>
                                        <div class="icon-star" title="Bấm để gắn dấu sao bảng này. Bảng này sẽ được thêm vào danh sách được đánh dấu sao của bạn.">
                                            <i class="ri-star-line"></i></div>
                                    </div>
                                   
                                </li>   
                            </ul>
                        </div>
                    </div>


                    <div class="working_space">
                        <p>CÁC KHÔNG GIAN LÀM VIỆC CỦA BẠN</p>
                        <div class="working_space-container" v-for="(item,index) in listWorkingSpaceContainer" :key="index">
                            <div class="working_space-header">
                             <div class="logo">
                                <div class="avatar"><p>G</p></div>
                                <div class="name">Tên project</div>
                             </div>

                             <div class="list_options">
                                 <a class="list_options-item">
                                    <i class="ri-layout-line"></i>
                                    <p>Bảng</p>
                                 </a>
                                 <a class="list_options-item">
                                    <i class="ri-time-line"></i>
                                    <p>Dạng xem</p>
                                 </a>
                                 <a class="list_options-item">
                                    <i class="ri-user-line"></i>
                                    <p>Thành viên</p>
                                 </a>
                                 <a class="list_options-item">
                                    <i class="ri-settings-3-line"></i>
                                    <p>Cài đặt</p>
                                 </a>
                                 <a class="list_options-item">
                                    <img src="" alt="">
                                    <p>Nâng cấp</p>
                                 </a>
                             </div>
                        </div>
                        <div class="working_space-content">
                            <div class="working_space-content-item">
                                    <a href="">
                                        <p>PROJECT1</p>
                                    </a>
                                    <div class="item-icon">
                                        <div class="icon-active" title="Có hoạt động mới trong bảng này."></div>
                                        <div class="icon-star" title="Bấm để gắn dấu sao bảng này. Bảng này sẽ được thêm vào danh sách được đánh dấu sao của bạn.">
                                            <i class="ri-star-line"></i></div>
                                    </div>
                            </div>

                            <div class="working_space-content-create">
                                 <p>Tạo bảng mới</p>
                            </div>


                        </div>
                        </div>
                     
                    </div>

                    <div class="view_table_close">
                             <p>Xem tất cả các bảng đã đóng</p>
                    </div>

                   <p></p>
                </div>
                <!-- End Page-content -->
            </div>
            <!-- end main content-->
            <Footer />
        </div>
        <!-- END layout-wrapper -->
    </div>
</template>

<!-- <style lang="scss">
    // @import "~bootstrap/scss/maps";
    // @import "~bootstrap/scss/utilities";
    // @import '../../../../resources/assets/scss/app.scss';
    // @import '~@/app.scss';    
    .col-1, .col-2, .col-3, .col-4, .col-5, .col-6, .col-7, .col-8, .col-9, .col-10, .col-11, .col-12, .col, .col-auto, .col-sm-1, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-sm-10, .col-sm-11, .col-sm-12, .col-sm, .col-sm-auto, .col-md-1, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-md-10, .col-md-11, .col-md-12, .col-md, .col-md-auto, .col-lg-1, .col-lg-2, .col-lg-3, .col-lg-4, .col-lg-5, .col-lg-6, .col-lg-7, .col-lg-8, .col-lg-9, .col-lg-10, .col-lg-11, .col-lg-12, .col-lg, .col-lg-auto, .col-xl-1, .col-xl-2, .col-xl-3, .col-xl-4, .col-xl-5, .col-xl-6, .col-xl-7, .col-xl-8, .col-xl-9, .col-xl-10, .col-xl-11, .col-xl-12, .col-xl, .col-xl-auto {
        position: relative;
        // padding-right: 12px;
        // padding-left: 12px;
    } 
    #app{
        position: relative;
    }
    ul{
        margin: 0 !important;
      padding: 0 !important;
      list-style: none;
    }
    a{
        text-decoration:none;
    }

    p{
        margin: 0 !important;
    }

    .main-content{
        margin-left: 260px;
        overflow: hidden;
        width: 75%;
        @media (max-width: 992px) {
            width: 85% !important;
            padding-left: 15%;
    }
        @media (max-width: 750px) {
            width: 95% !important;
            padding-left: 5%;
            font-size: 13px;
         }
    
     .page-content{
        margin-left: 35px;
        max-width: 1623px;
        width: 100%;
        margin-left: 0%;
        .recently-viewed-title{
            display: flex;
            align-items: center;
            i{
                 font-size: 20px;
            }
            p{
                padding-left: 9px;
            }
        }
        .recently-viewed-content{
            margin-top: 2%;
        }
        .list_item{
        display: flex;
        flex-wrap: wrap;
        @media (max-width: 992px) {
            column-gap: 2%;
            row-gap: 3vw;

        }
        .item:hover{
            opacity: 0.6;
            transform: scale(1.06);
        }
        .item{
            width: 23%;
            margin-left: 2%;
            background: #abbfbf;
            height: 150px;
            cursor: pointer;
            position: relative;
            @media (max-width: 992px) {
                width: 47%;
        }
             @media (max-width: 750px) {
                height: 100px;
              }

            a{
                text-decoration:none !important;
                p{
                    width: 100%;
                    height: 150px;
                    padding: 6% 8%;
                    color: #fff;
                    background: #abbfbf;
                    @media (max-width: 750px) {
                    height: 100px;
                    }
                    
                }
            }
            .item-icon{
                display: flex;
                position: absolute;
                bottom: 5%;
                right: 4%;
                align-items: center;
                .icon-active{
                background: #fff;
                width: 20px;
                height: 20px;
                border-radius: 100%;
            }
            .icon-star{
                margin-left: 5px;
                display: none;
            }
            }
         
        }
        .item:hover {
            .icon-star{
                display: block !important;
            }
             
         }

        }

        .working_space{
            margin-top: 4%;
            padding-bottom: 4%;
            p{
                font-weight: 700;
                color: #5e6c84;
            }
            
            .working_space-container:first-child.working_space-header{
                margin-top: 4% !important;
            }
            
            .working_space-header{
                display: flex;
                align-items: center;
                justify-content: space-between;
                width: 97%;
                margin-top: 4%;
                @media (max-width: 750px) {
                    flex-wrap: wrap;
                    margin-top: 12%;
                 }
                .logo{
                    width: 19%;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    @media (max-width: 992px) {
                        width: 25%;
                    }
                    @media (max-width: 750px) {
                        width: 45%;
                    }
                    .avatar{
                        width: 33%;
                        height: 4.5vw;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 2vw;
                        background: antiquewhite;
                        @media (max-width: 750px) {
                          height: 9.5vw;
                          font-size: 5vw;
                      }
                        p{
                        margin: 0;
                    }
                    }
                    .name{
                        width: auto;
                    }
                }

                .list_options{
                    display: flex;
                    width: 70%;
                    justify-content: space-between;
                    column-gap: 2%;
                    @media (max-width: 992px) {
                        flex-wrap: wrap;
                        column-gap: 2vw;
                        row-gap: 2vw;
                         justify-content: center;
                    }
                    @media (max-width: 750px) {
                        width: 100%;
                        justify-content: flex-start;
                    }
                    .list_options-item:hover{
                        opacity: 0.6;
                        transform: scale(1.06);
                        cursor: pointer;
                    }
                   .list_options-item{
                    background: #f0f2f5;
                    padding: 2% 3%;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    color: black;
                    text-decoration:none;
                    text-overflow: ellipsis;
                     white-space: nowrap;
                     overflow: hidden;
                    @media (max-width: 992px) {
                        width: 45%;
                    }
                    i{
                        font-size: 20px;
                    }
                    p{
                        margin: 0;
                        margin-left: 3px !important;
                    }
                   }

                   .list_options-item:nth-child(5){
                      background: #eddbf4;
                   }
                }
            }

            .working_space-content{         
                display: flex;
                margin-top: 2%;
                width: 48%;
                justify-content: space-between;
                @media (max-width: 992px) {
                        width: 70%;
                    }
                 @media (max-width: 750px) {
                     width: 100%;
                     margin-top: 4%;
                 }
                .working_space-content-item:hover{
                        opacity: 0.6;
                        transform: scale(1.06);
                        cursor: pointer;
                        .icon-star{
                            display: block !important;
                        }
                    }
                 .working_space-content-item{
                    width: 47%;
                   display: flex;
                   justify-content: center;
                   align-items: center;
                    height: 150px;
                    background: antiquewhite;
                    position: relative;
                    @media (max-width: 750px) {
                        height: 100px;
                    }
                     a{
                        display: block;
                        width: 100%;
                        height: 150px;
                        text-decoration: none;
                        @media (max-width: 750px) {
                            height: 100px;
                         }
                        p{
                            padding-top: 6%;
                            padding-left: 9%;
                            color: #fff;
                        }
                     }

                     .item-icon{
                display: flex;
                position: absolute;
                bottom: 5%;
                right: 4%;
                align-items: center;
                .icon-active{
                background: #fff;
                width: 20px;
                height: 20px;
                border-radius: 100%;
            }
              .icon-star{
                margin-left: 5px;
                display: none;
               }
            }

          
                 }
                 .working_space-content-create:hover{
                        opacity: 0.6;
                        transform: scale(1.06);
                        cursor: pointer;
                    }
                 .working_space-content-create{
                    width: 48%;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 150px;
                    background: aliceblue;
                    @media (max-width: 750px) {
                            height: 100px;
                         }
                 }
            }
        }
        .view_table_close:hover{
                        opacity: 0.6;
                        transform: scale(1.06);
                        cursor: pointer;
                    }

        .view_table_close{
            width: 30%;
            background: aliceblue;
            text-align: center;
            padding: 15px 0;
            width: 259px;
            
        }
        
    }
    }
   
   
</style> -->