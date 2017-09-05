import fetch from 'utils/fetch';

export function loginByEmail(email, password) {
  const data = {
    email,
    password
  };
  return fetch({
    url: '/login/loginbyemail',
    method: 'post',
    data
  });
}

export function loginByUserName(user, password) {
  const data = {
    admin_name: user,
    admin_password: password,
    //client_id: process.env.PASSPORT_CLIETN_ID,
    //client_secret: process.env.PASSPORT_CLIENT_SECRET,
    //grant_type: process.env.PASSPORT_GRANT_TYPE,
    //scope: ''
  };
  return fetch({
    url: '/login',
    method: 'post',
    data
  })
}

export function logout() {
  return fetch({
    url: '/logout',
    method: 'post'
  });
}

export function getInfo(token) {
  return fetch({
    url: '/user/info',
    method: 'get',
    params: { token }
  });
}
