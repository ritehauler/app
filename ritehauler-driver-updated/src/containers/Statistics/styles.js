import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../theme";

export default StyleSheet.create({
  containerStyle: {
    flex: 1,
    backgroundColor: Colors.background.login
  },
  navigationContainerStyle: {
    backgroundColor: Colors.background.primary
  },
  navigationSeparatorStyle: {
    marginHorizontal: Metrics.baseMargin
  }
});
