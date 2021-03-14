import api from "../../utils/api";

function create(name) {
  return api.post("/api/task", {
    name
  });
}

function current() {
  return api.get("/api/task/current");
}

function stop(taskId, description) {
  return api.post(`/api/task/stop/${taskId}`, {
    description
  }).then((response) => {
    return response.data;
  });
}

function getAll(dates) {
  let data = null;

  if (dates) {
    data = {
      start: dates[0],
      end: dates[1],
    };
  }

  return api.get(`/api/task`, {
    params: data
  }).then((response) => {
    return response.data.tasks;
  });
}

function download(dates) {
  let data = null;

  if (dates) {
    data = {
      start: dates[0],
      end: dates[1],
    };
  }

  return api.getUri({
    url: process.env.VUE_APP_API_URL + '/api/task/download',
    params: data
  });
}

export default {
  create,
  current,
  stop,
  getAll,
  download
};
