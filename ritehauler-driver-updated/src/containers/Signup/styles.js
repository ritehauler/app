import { StyleSheet, Dimensions } from "react-native";
import { Colors, Metrics } from "../../theme";
import { color } from "react-native-reanimated";
import { registerMessageBar } from "react-native-message-bar/MessageBarManager";
var { width, height } = Dimensions.get('window');

export default StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: Colors.background.primary,
    marginHorizontal:12,
    height: height * 0.85,
    // borderWidth: 2,
  },

  eachBox:{
    // flex: 1,
    flexDirection: 'row',
    // borderWidth: 2
    // alignItems: 'center',
    // justifyContent: 'center'
  },
  leftContainer: {
    flex: 1,
    marginRight:5,
    marginTop: 5,
    // borderWidth: 1
  },

  rightContainer: {
    flex: 1,
    marginLeft:5,
    marginTop: 5,
    // borderWidth: 1
  },

  buttonContainer: {
    flex: 1,
    // margin:5,
  },
  buttonWrapper:{
    marginTop: -Metrics.ratio(2)
  },
  button: {
    marginBottom: Metrics.smallMargin,
    marginTop: Metrics.smallMargin,
  },
  textView: {
    alignItems: 'center',
    justifyContent: 'center',
    // borderWidth: 1,
    marginTop: height * 0.01
  },
  textStyle:{
    fontSize: width * 0.033,
    textAlign:"center",
    color:Colors.text.navBarText,
    // lineHeight: height * 0.02
  },

  blackText:{
      fontSize:14,
      color:Colors.text.primary,
  },

  viewSign:{
    bottom: 0,
    justifyContent: 'flex-end',
    flex: 1,
    // borderWidth :1
  },

  signText:{
    fontSize:14,
    textAlign:"center",
    color:Colors.text.primary,
  },
  
  signTextTwo:{
    fontSize:18,
    color:Colors.status.accepted,
  },

  errorMsg:{
  fontSize:10,
  color:Colors.status.cancelled,
  },

  errorBorder:{
  // borderColor:,
  borderRadius:1,
  borderWidth:2,
  borderColor:Colors.text.tertiary,
  borderBottomColor:Colors.status.cancelled,
 },

 box:{
   borderWidth:1,
   height:56,
   width :'100%',
   borderColor:Colors.border
 },
 scrollStyle:{
  flex:1
 },

 textInput: {
   paddingVertical: width * 0.04,
   paddingLeft: width * 0.02,
   borderWidth: 1,
   borderColor: Colors.border,
   marginVertical: height * 0.008,
 },
 button: {
  //  borderWidth: 1,
    marginTop: height * 0.01,
    marginHorizontal: width * 0.01,
    height: height * 0.06,
  //  fontSize: width * 0.0
 },
 error: {
   color: 'red',
   fontSize: width * 0.03,
   paddingRight: 5,
  //  borderWidth: 1
 },

 errorView: {
  alignItems: 'center',
  // borderWidth: 1,
  marginTop: height* 0.1
 },
 message: {
   color: 'white',
   fontSize: width * 0.04,
   backgroundColor: 'black',
   opacity: 0.8,
   paddingHorizontal: width * 0.05,
   paddingVertical: width * 0.02,
   borderRadius: 10,
 }
 });