import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../theme";
import Utils from "../../util";

export default StyleSheet.create({
  containerStyle: {
    backgroundColor: Colors.background.login,
    flex: 1
  },
  safeAreaView: {
    flex: 1,
    backgroundColor: Colors.background.login
  },
  footerWrapper: {
    marginTop: Metrics.baseMargin,
    flexDirection: "row",
    backgroundColor: Colors.background.primary
  },
  basePadding: {
    padding: Metrics.baseMargin
  }
});
