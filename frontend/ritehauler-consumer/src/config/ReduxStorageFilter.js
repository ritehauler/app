import filter from "redux-storage-decorator-filter";
import createEngine from "redux-storage-engine-reactnativeasyncstorage";

const REDUX_STORAGE = filter(
  createEngine("AppTree"),
  [
    "whitelisted-key",
    ["user", "data"],
    ["stateCity", "data"],
    ["generalSettings", "data"]
  ],
  []
);

export default REDUX_STORAGE;
