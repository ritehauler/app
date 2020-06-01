// @flow
import { StyleSheet } from "react-native";
import { Metrics, Colors } from "../../../theme";

export default StyleSheet.create({
  container: {
    backgroundColor: Colors.background.secondary,
    padding: Metrics.baseMargin,
    marginTop: Metrics.smallMargin * 1.2
  },
  emptyView: {
    alignItems: "center",
    justifyContent: "center",
    marginVertical: Metrics.baseMargin
  },
  info: {
    marginTop: Metrics.baseMargin,
    lineHeight: Metrics.baseMargin * 1.25,
    marginHorizontal: Metrics.smallMargin / 2
  },
  volume: {
    marginTop: Metrics.smallMargin * 0.75
  },
  cartonImage: {
    flex: 1,
    alignItems: "center"
  },
  loadingView: {
    alignItems: "center",
    justifyContent: "center",
    paddingVertical: Metrics.baseMargin * 4
  },
  image: {
    width: 200,
    height: 200
  }
});
