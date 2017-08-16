import axios from 'axios';
import { Message } from 'element-ui';
import store from '../store';
import { getToken } from 'utils/auth';

// 创建axios实例
const service = axios.create({
  baseURL: process.env.BASE_API, // api的base_url
  timeout: 5000,                  // 请求超时时间
  headers: {                      // laravel csrf-token
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    'X-Requested-With': 'XMLHttpRequest'
  }
});

// request拦截器
service.interceptors.request.use(config => {
  // Do something before request is sent
  if (store.getters.token) {
    // laravel passport的请求头
    config.headers.Authorization = 'Bearer ' + getToken();
    config.headers.Accept = 'application/json';
  }
  return config;
}, error => {
  // Do something with request error
  console.log(error); // for debug
  Promise.reject(error);
});

// respone拦截器
service.interceptors.response.use(
  response => response,
  /**
  * 下面的注释为通过response自定义code来标示请求状态，当code返回如下情况为权限有问题，登出并返回到登录页
  * 如通过xmlhttprequest 状态码标识 逻辑可写在下面error中
  */
//  const res = response.data;
//     if (res.code !== 20000) {
//       Message({
//         message: res.message,
//         type: 'error',
//         duration: 5 * 1000
//       });
//       // 50008:非法的token; 50012:其他客户端登录了;  50014:Token 过期了;
//       if (res.code === 50008 || res.code === 50012 || res.code === 50014) {
//         MessageBox.confirm('你已被登出，可以取消继续留在该页面，或者重新登录', '确定登出', {
//           confirmButtonText: '重新登录',
//           cancelButtonText: '取消',
//           type: 'warning'
//         }).then(() => {
//           store.dispatch('FedLogOut').then(() => {
//             location.reload();// 为了重新实例化vue-router对象 避免bug
//           });
//         })
//       }
//       return Promise.reject(error);
//     } else {
//       return response.data;
//     }
  error => {
    let msg = error.message;
    if (typeof error.response.data === 'object') {
      this.errors = _.flatten(_.toArray(error.response.data));
      console.log(this.errors);
      if (this.errors[0]) {
        msg = this.errors[0];
      }
    }
    Message({
      message: msg,
      type: 'error',
      duration: 5 * 1000
    });
    console.log('err' + error);// for debug
    return Promise.reject(error);
  }
)

export default service;
