// @flow
import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../theme";

export default StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: Colors.background.primary
  },
  customButtonView: {
    flexDirection: "row",
    alignItems: "center"
  },
  amountButton: {
    flex: 1
  },
  imageArrowButton: {
    marginRight: Metrics.baseMargin
  }
});
