import { StyleSheet } from "react-native";
import { Colors, Metrics, ApplicationStyles } from "../../theme";

export default StyleSheet.create({
  titleStyle: {
    backgroundColor: Colors.background.login,
    paddingTop: Metrics.baseMargin +Metrics.smallMargin * 0.2,
    paddingBottom:Metrics.smallMargin 
  }
});
