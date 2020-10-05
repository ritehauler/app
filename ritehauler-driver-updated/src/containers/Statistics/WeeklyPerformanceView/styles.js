import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../../theme";

export default StyleSheet.create({
  containerStyle: {
    backgroundColor: Colors.background.login
  },
  scrollTabStyle: {
    backgroundColor: Colors.background.primary
  },
  cellContainerStyle: {
    paddingVertical: Metrics.baseMargin+Metrics.smallMargin
  },
  cellStyle: {
    paddingBottom: Metrics.listSpace * 2
  }
});
