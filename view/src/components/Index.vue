<template>
  <div class="hello">

    <div class="block">
      <div class="row header  ">
        <div class="right pull-right">
          <ul class="inline pull-right">
            <li>
                <router-link :to="link">{{link_text}}</router-link>
            </li>
          </ul>
          </div>  
        </div>

      <el-carousel :height="height" :autoplay="false" indicator-position="none" arrow="never">
        <el-carousel-item style="background-color: #2393d6;">

          <div class="slide">
              <img src="static/logo/b_64.png" alt="">
              <h2 v-html="$t('section_title1')"></h2>
              <p><span v-html="$t('section_description1')"></span></p>
          </div>
        </el-carousel-item>
      </el-carousel>
      
    </div>
  </div>
</template>

<script>
export default {
  name: 'Index',
  data () {
    return {
      height: '',
      link:'',
      link_text:''
    }
  },
  methods:{
    getHeight(){
       if (window.innerHeight){
          var winHeight = window.innerHeight;
       }   
       else if ((document.body) && (document.body.clientHeight)){
          var winHeight = document.body.clientHeight;
       }
        this.height = winHeight+'px' ;
      }
  },
  mounted () {
    var that = this ;
    this.getHeight();
    that.link = '/user/login';
    that.link_text = that.$t("index_login");
    
    var userInfo = this.$cookies.get('user_info'); 
        console.log(userInfo);
    if (userInfo) {
        that.link = '/item/index';
        that.link_text = that.$t("my_item");
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>


  .el-carousel__item { 
    text-align: center; 
    font: 25px "Microsoft Yahei"; 
    color: #fff;
  }

  .header{
   padding-right: 100px;
   padding-top: 30px;
   font-size: 18px; 
   position: fixed;
      right: 0;
      left: 0;
      z-index: 1030;
      margin-bottom: 0;
  }
  .header a {
      color: white;
      font-size: 14px;
  }
  .slide{
    width: 980px;
    position  : absolute;
    top       : 50%;
    left      : 50%;
    transform : translate(-50%,-50%);
    padding-top: 0px;
    padding-left: 15px;
    padding-right: 15px;
    padding-bottom: 0px;
    box-sizing: border-box;
  }

</style>
