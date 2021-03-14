import api from "../../utils/api";

function create(taskId) {
  return api.post(`/api/pause/${taskId}`);
}

function end(pauseId) {
  return api.post(`/api/pause/end/${pauseId}`);
}

export default {
  create,
  end
};
