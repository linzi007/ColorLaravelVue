import fetch from 'utils/fetch';

export function fetchList(query, url) {
  return fetch({
    url,
    method: 'get',
    params: query
  });
}

export function fetchCreate(data, url) {
  return fetch({
    url,
    method: 'post',
    data
  });
}

export function fetchUpdate(data, url) {
  return fetch({
    url,
    method: 'put',
    data
  })
}

// 删除
export function fetchDelete(url) {
  return fetch({
    url,
    method: 'delete'
  })
}
