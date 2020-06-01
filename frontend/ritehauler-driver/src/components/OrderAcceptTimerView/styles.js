import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../theme";

export default StyleSheet.create({
  timerAnimatedContainerStyle: {
    backgroundColor: Colors.background.primary,
    position: "absolute",
    top: 0,
    left: 0,
    right: 0
  },
  timerViewContainerStyle: {
    flex: 1
  },
  timerTextStyle: {
    alignSelf: "center",
    padding: Metrics.baseMargin
  },
  declineButtonStyle: {
    position: "absolute",
    right: 0,
    top: 0,
    bottom: 0,
    justifyContent: "center",
    padding: Metrics.baseMargin
  }
});
