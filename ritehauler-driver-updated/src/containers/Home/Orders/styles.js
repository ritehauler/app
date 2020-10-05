import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../../theme";

export default StyleSheet.create({
  containerStyle: {
    flex: 1,
    backgroundColor: Colors.background.login
  },
  listSeparatorStyle: {
    backgroundColor: Colors.background.login,
    height: Metrics.baseMargin
  },
  list: {
    backgroundColor: Colors.background.login,
    marginHorizontal: Metrics.baseMargin
  },
  listContainer: {
    paddingBottom: Metrics.baseMargin
  },
  buttonStyle: {
    position: "absolute",
    left: 0,
    right: 0,
    bottom: 0
  },
  emptyWrapper: { flex: 1, backgroundColor: Colors.background.login }
});
