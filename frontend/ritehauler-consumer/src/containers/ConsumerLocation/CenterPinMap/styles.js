// @flow
import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../../theme";

export default StyleSheet.create({
  container: {
    position: "absolute",
    top: 0,
    left: 0,
    right: 0,
    bottom: 0,
    alignItems: "center",
    justifyContent: "center"
  },
  animatableView: {
    alignItems: "center",
    justifyContent: "flex-end",
    height: Metrics.baseMargin * 2.5,
    marginTop: Metrics.smallMargin * -5.25
  },
  image: {
    width: Metrics.baseMargin * 2.25,
    height: Metrics.baseMargin * 2.25
  },
  textContainer: {
    backgroundColor: Colors.background.secondary,
    paddingHorizontal: Metrics.baseMargin,
    borderRadius: Metrics.smallMargin * 6.25,
    paddingVertical: Metrics.smallMargin,
    flexDirection: "row"
  },
  connectLine: {
    width: Metrics.smallMargin / 4,
    height: Metrics.smallMargin / 2,
    backgroundColor: Colors.background.accent
  },
  pinCircle: {
    width: Metrics.smallMargin,
    height: Metrics.smallMargin,
    borderRadius: Metrics.smallMargin / 2,
    borderWidth: Metrics.smallMargin / 4,
    borderColor: Colors.background.accent
  },
  retryIcon: {
    marginLeft: Metrics.smallMargin,
    width: Metrics.baseMargin,
    height: Metrics.baseMargin
  },
  loaderView: {
    backgroundColor: Colors.background.accent,
    width: Metrics.baseMargin * 2,
    height: Metrics.baseMargin * 2,
    borderRadius: Metrics.baseMargin
  },
  progressBar: {
    position: "absolute",
    right: -3,
    top: -3
  }
});
