import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../theme";

export default StyleSheet.create({
  cellContainerStyle: {
    backgroundColor: Colors.background.login,
    paddingHorizontal: Metrics.baseMargin,
    flexDirection: "row",
    flexWrap: "wrap"
  },
  cellStyle: {
    width: Metrics.graphCellWidth,
    borderWidth: Metrics.ratio(1),
    borderColor: Colors.separator,
    backgroundColor: Colors.background.primary,
    padding: Metrics.listSpace
  },
  imageStyle: {
    alignSelf: "flex-end"
  }
});
