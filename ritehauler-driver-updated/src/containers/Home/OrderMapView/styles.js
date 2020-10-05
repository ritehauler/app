import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../../theme";

export default StyleSheet.create({
  containerStyle: {
    ...StyleSheet.absoluteFillObject,
    flex: 1,
    backgroundColor: Colors.background.primary,
    alignItems: "center",
    justifyContent: "center"
  },
  mapStyle: {
    ...StyleSheet.absoluteFillObject
  },
  bottomViewStyle: {
    position: "absolute",
    bottom: 0,
    left: 0,
    right: 0
  },
  currentLocationContainerStyle: {
    position: "absolute",
    bottom: 0,
    right: 0,
    margin: Metrics.smallMargin * 1.5,
    width: Metrics.stuckLocationImageSize,
    height: Metrics.stuckLocationImageSize,
    borderRadius: Metrics.stuckLocationImageRadius,
    alignItems: "center",
    justifyContent: "center",
    elevation: Metrics.baseMargin,
    shadowColor: Colors.black,
    shadowOffset: {
      width: 0,
      height: Metrics.ratio(1)
    },
    shadowOpacity: 0.18,
    shadowRadius: Metrics.ratio(1)
  },
  stuckContainerStyle: {
    position: "absolute",
    bottom: 0,
    left: 0,
    margin: Metrics.smallMargin * 1.5,
    width: Metrics.stuckLocationImageSize,
    height: Metrics.stuckLocationImageSize,
    borderRadius: Metrics.stuckLocationImageRadius,
    alignItems: "center",
    justifyContent: "center",
    elevation: Metrics.baseMargin,
    shadowColor: Colors.black,
    shadowOffset: {
      width: 0,
      height: Metrics.ratio(1)
    },
    shadowOpacity: 0.18,
    shadowRadius: Metrics.ratio(1)
  },
  buttonStyle: {
    position: "absolute",
    left: 0,
    right: 0,
    bottom: 0
  },
  timerContainerStyle: {
    position: "absolute",
    left: 0,
    right: 0,
    top: 0,
    flexDirection: "row",
    paddingVertical: Metrics.ratio(6),
    paddingHorizontal: Metrics.baseMargin,
    backgroundColor: Colors.background.underlineColor,
    alignItems: "center",
    justifyContent: "center"
  },
  timerStyle: {
    paddingLeft: Metrics.ratio(4)
  },
  activityIndicatorWrapper: {
    position: "absolute",
    right: Metrics.baseMargin * 1.6,
    top: Metrics.doubleBaseMargin,
    backgroundColor: Colors.transparent
  }
});
