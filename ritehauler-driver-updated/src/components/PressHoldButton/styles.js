import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../theme";

export default StyleSheet.create({
  containerStyle: {
    backgroundColor: Colors.background.accent
  },
  textContainerStyle: {
    padding: Metrics.pressHoldButtonPadding
  },
  titleStyle: {
    textAlign: "center"
  },
  fillViewStyle: {
    height: Metrics.ratio(5),
    backgroundColor: Colors.background.progressBackgroundColor
  }
});
