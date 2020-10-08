import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../../theme";

export default StyleSheet.create({
  containerStyle: {
    flexDirection: "row",
    backgroundColor: Colors.background.primary,
    marginHorizontal: Metrics.baseMargin,
    paddingHorizontal: Metrics.baseMargin,
    paddingVertical: Metrics.baseMargin,
    justifyContent: "center"
  },
  titleTextStyle: {
    flex: 1
  },
  imageStyle: {
    alignSelf: "center"
  }
});
