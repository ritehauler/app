// @flow
import * as storage from "redux-storage";
import { createLogger } from "redux-logger";
import createSagaMiddleware from "redux-saga";
import { compose, createStore, applyMiddleware } from "redux";

import sagas from "../sagas";
import reduxStorageFilter from "../config/ReduxStorageFilter";

const isDebuggingInChrome = __DEV__ && !!window.navigator.userAgent;

const logger = createLogger({
  predicate: () => isDebuggingInChrome,
  collapsed: true,
  duration: true,
  diff: true
});

export default function configureStore(reducers, onComplete: Function) {
  const storeMiddleware = storage.createMiddleware(reduxStorageFilter);
  const sagaMiddleware = createSagaMiddleware();

  const store = createStore(
    storage.reducer(reducers),
    __DEV__
      ? compose(applyMiddleware(sagaMiddleware, storeMiddleware, logger))
      : compose(applyMiddleware(sagaMiddleware, storeMiddleware))
  );

  if (isDebuggingInChrome) {
    window.store = store;
  }

  const load = storage.createLoader(reduxStorageFilter);
  load(store)
    .then(onComplete)
    .catch(() => {
      //console.log("Failed to load previous state @ configureStore.js#44")
    });

  sagaMiddleware.run(sagas);

  return store;
}
