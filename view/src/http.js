/**
 * http配置
 */
import Vue from 'vue'
import axios from 'axios'
import router from '@/router/index'

// axios 配置
axios.defaults.timeout = 20000;
axios.defaults.baseURL = DocConfig.server;

function getcookie(Name) {
    //查询检索的值
    var search = Name + "=";
    //返回值
    var returnvalue = "";
    if (document.cookie.length > 0) {
        var sd = document.cookie.indexOf(search);
        if (sd!= -1) {
            sd += search.length;
            var end = document.cookie.indexOf(";", sd);
            if (end == -1){
                end = document.cookie.length;
            }
                
            //unescape() 函数可对通过 escape() 编码的字符串进行解码。
            returnvalue=JSON.parse(unescape(document.cookie.substring(sd, end)))
        }
    } 
    return returnvalue;
}

var userInfo = getcookie('user_info');
var token = ' ';
if(userInfo){
   token = userInfo.token 
}

// http request 拦截器
axios.interceptors.request.use(
    config => {
        //
        //console.log(config.data);
        var configtoken = config.data.get('token');
        if(!configtoken){
            if(token){
                config.data.append('token', token)
            }  
        }
        
        return config;
    },
    err => {
        return Promise.reject(err);
    });

// http response 拦截器
axios.interceptors.response.use(
    response => {
        if (response.config.data && response.config.data.indexOf("redirect_login=false") > -1 ) {
            //不跳转到登录
        } 
        if(response.data.code === '400') {
            setTimeout(function(){
                router.replace({
                    path: '/user/login',
                    query: {redirect: router.currentRoute.fullPath}
                });
            }, 2000);
        } 
        
        return response;
    },
    error => {
        // console.log(JSON.stringify(error));//console : Error: Request failed with status code 402
        return Promise.reject(error.response.data)
    });

export default axios;