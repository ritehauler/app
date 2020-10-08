// @flow
import { connect } from "react-redux";
import { Dimensions, Button, View, Image, Keyboard, NativeModules ,TouchableOpacity, TextInput} from "react-native";
import React, { Component } from "react";
import { Actions } from "react-native-router-flux";
import { Text, BottomButton, FloatLabelTextInput } from "../../components";
import styles from "./styles";
import { Images, ApplicationStyles, Metrics, Colors } from "../../theme";
import Utils from "../../util";
import helper from "../../util/helper";
import { API_ENTITY_AUTH_EMAIL_LOGIN } from "../../config/WebService";
import { request as userRequest } from "../../actions/UserActions";
import {
  WithKeyboardSubscription,
  WithKeyboardUnSubscription
} from "../HOC/WithKeyboardListener";
import { USER_ENTITY_TYPE_ID } from "../../constant";
import WithLoader from "../HOC/WithLoader";
import { color } from "react-native-reanimated";
// import ThankyouImg from '../../assets/images/thankyouImg.jpeg';
var { width, height } = Dimensions.get('window');

const GPSTracker = NativeModules.GPSTracker;

class Login extends Component {
  state = {
    isKeyboardVisible: false,
    errorEmail: false,
    errorPass: false,

    emailInput: '',
    passInput: '',

    enable: false
  };

  componentWillMount() {
    WithKeyboardSubscription(this);
  }

  componentWillUnmount() {
    WithKeyboardUnSubscription(this);
  }

  forgotScreen() {
    Keyboard.dismiss();
    setTimeout(() => {
      Actions.forgot();
    }, 200);
  }
  signup() {
    Keyboard.dismiss();
    setTimeout(() => {
      Actions.signup();
    }, 200);
  }

  validateEmail = email => {
    let re = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
    return re.test(email);
  };

  loginUser = () => {
    // alert(this.state.emailInput)
    // const { errorEmail, errorPass } = this.state;
    const {emailInput, passInput } = this.state;
    !emailInput ? this.setState({ emailError: 'Email require'}) : '';
    !passInput ? this.setState({ passwordError: 'Password require'}) : '';
    !this.validateEmail(emailInput) ? this.setState({ emailError: "Ex: name@domain.com" }) : "";

    // if (
    //   helper.isValidEmail(this.state.emailInput) &&
    //   helper.isValidPass(this.state.passInput)
    // ) 
    if(emailInput && passInput && this.validateEmail(emailInput) ) 
    {
      Keyboard.dismiss();
      this.props.userRequest(API_ENTITY_AUTH_EMAIL_LOGIN, {
        entity_type_id: USER_ENTITY_TYPE_ID,
        login_id: this.state.emailInput,
        password: this.state.passInput
        // device_udid: "aaaaa",
        // device_token: "11112222333"
    });
    } 
    // else {
    //   let validEmail = helper.isValidEmail(this.state.emailInput);
    //   let validPass = helper.isValidPass(this.state.passInput);

    //   // let validEmail = "test1@gmail.com";
    //   // let validPass = 1234567;
     
    //   if (!validEmail) {
    //     alert()
    //     this.setState({ emailError: 'Email is required'})
    //     // this.emailInput.setError(true);
    //     // this.emailInput.focus();
    //   }
    //   if (!validPass) {
    //     // this.passInput.setError(true);
    //     // if (validEmail) this.passInput.focus();
    //   }
    // }
  };

  renderHeader() {
    const { isKeyboardVisible } = this.state;
    if (!isKeyboardVisible) {
      return (
        <View style={{ paddingBottom: Metrics.ratio(50) }}>
          <Image source={Images.logo} style={styles.logo} />
          <Text
            style={[
              ApplicationStyles.dBold26,
              {
                paddingBottom: Utils.isPlatformAndroid()
                  ? Metrics.ratio(6)
                  : Metrics.ratio(8)
              }
            ]}
          >
            Rite Hauler Driver
          </Text>
          <Text style={ApplicationStyles.re16Black}>Sign in to continue</Text>
        </View>
      );
    }
    return null;
  }

  renderEmail() {
    return (
      <TextInput
        returnKeyType="next"
        value={this.state.emailInput}
        onChangeText={(value)=>this.setState({emailInput:value})} 
        ref={ref => { this.emailInput = ref }}
        errorType="email"
        placeholderTextColor='grey'
        blurOnSubmit={false}
        errorMessage="Enter valid email address"
        keyboardType="email-address"
        autoCapitalize="none"
        // onSubmitEditing={() => this.passInput.focus()}
        placeholder="Email address"
        style={styles.textInput}
        onFocus={() => this.setState({ emailError: null })}

      />
    );
  }

  renderPassword() {
    return (
      <View style={styles.textInputView}>
      <TextInput
        secureTextEntry
        returnKeyType="done"
        placeholderTextColor='grey'
        value={this.state.passInput}
        onChangeText={(value)=>this.setState({passInput:value})} 
        errorMessage="Password length must be greater then 6"
        // onRightTextPress={this.forgotScreen}
        ref={ref => {
          this.passInput = ref;
        }}
        // onSubmitEditing={this.loginUser}
        autoCapitalize="none"
        placeholder="Password"
        style={styles.passtextInput}
        onFocus={() => this.setState({ passwordError: null })}
      />
      <TouchableOpacity onPress={() => Actions.forgot()} style={{justifyContent:'center'}}>
        <Text style={styles.forgotText}> Forgot?</Text>
      </TouchableOpacity>
      </View>
    );
  }

  render() {
    return (
      <View style={styles.container}>
        {this.renderHeader()}
        {this.renderEmail()}
                      {this.state.emailError && 
                        <Text style={styles.error}>{this.state.emailError}</Text>
                      }

        {this.renderPassword()}
                      {this.state.passwordError && 
                        <Text style={styles.error}>{this.state.passwordError}</Text>
                      }

        <View style={styles.buttonWrapper}>
          {this.state.enable == false ?
            <View style={styles.disableButton}>
              <Text style={styles.disableTitleButton}> Sign in </Text>
            </View>
            :
            <BottomButton
              title="Sign in"
              style={styles.button}
              onPress={this.loginUser}
            /> 
          }
        </View>
        {/* <View>
            <ThankyouImg style={styles.thankyouImgStyle}/>
         </View> */}
        
        <TouchableOpacity
          onPress={this.signup}>
          <Text style={{color: 'grey', fontSize: width * 0.03, marginTop: height * 0.01}}>Create account</Text>
        </TouchableOpacity>
      </View>
    );
  }
 }

const mapStateToProps = ({ user }) => {
  const componentData = { ...user };
  return { componentData, modal: false };
};

const actions = {
  userRequest
};

export default connect(mapStateToProps, actions)(WithLoader(Login));
