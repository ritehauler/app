import { connect } from "react-redux";
import React, { Component } from "react";
import { View ,TextInput ,ScrollView, Keyboard, Alert, ToastAndroid, Platform } from "react-native";
import { Actions } from "react-native-router-flux";
import { Text, BottomButton, FloatLabelTextInput } from "../../components";
import { ApplicationStyles } from "../../theme";
import styles from "./styles";
import { request } from "../../actions/UserActions";
import { API_ENTITY_AUTH_FORGOT_PASS } from "../../config/WebService";
import WithLoader from "../HOC/WithLoader";
import helper from "../../util/helper";
import { USER_ENTITY_TYPE_ID } from "../../constant";
import Utils from "../../util";
import { last, truncate, reject } from "lodash";
import { IS_AUTH_EXISTS } from "../../constant";
import { IS_TEMP_PASSWORD } from "../../constant";
import Spinner from "react-native-loading-spinner-overlay";
import Toast from 'react-native-simple-toast';

class Signup extends Component{
 state={
  //  errorEmail:false,
  //   errorPassword:false,
  //   errorFirstNameInput:false,
  //   errorMobile_no:false,
  //   errorLast_name:false,
  //    isKeyboardVisible: false,
  //    emailInputSignUp:'',
  //    firstNameInput:'',
  //    lastNameInput:'',
  //    mobileNumberInput:'',
      // isLoading: false,

     first_name: '',
     last_name: '',
     email: '',
     mobile_no: '',
     password: '',
     conf_password: '',
    };
  
  
//  componentDidMount() {
//    alert(this.state.isLoading)
//  }
 
    login =()=> {
          Actions.login();
      }
     
    validateEmail = email => {
        let re = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
        return re.test(email);
    };

    userSignUp = () => {
      const { first_name, last_name, email, mobile_no, password, conf_password } = this.state;
      !first_name ? this.setState({ firstnameError: 'First name require'}) : '';
      !last_name ? this.setState({ lastnameError: 'Last name require'}) : '',
      !email ? this.setState({ emailError: 'Email require'}) : '';
      !mobile_no ? this.setState({ numberError: 'Mobile number require'}) : '';
      !password ? this.setState({ passwordError: 'Password require'}) : '';
      !conf_password ? this.setState({ conf_passwordError : 'Confirm password require'}) : '';
      
      !this.validateEmail(email) ? this.setState({ emailError: "Ex: name@domain.com" }) : "";
      // mobile_no.length <= 12 && mobile_no.length !== 0 ? this.setState({ numberError: 'Enter number in formate +1-0123456789'}) : '';
      password.length < 6 && password.length !== 0 ? this.setState({ passwordError: 'Password should be more than 6 character'}) : '';
      conf_password.length < 6 && conf_password.length !== 0 ? this.setState({ conf_passwordError: 'Confirm password should be more than 6 character'}) : '';

      if(first_name && last_name && email && this.validateEmail(email) && mobile_no && password && conf_password) {
        mobile_no.length <= 12 && mobile_no.length !== 0 ? 
        this.setState({ numberError: 'Enter number in formate +1-0123456789'})
        :
        this.setState({ isLoading: true })
          console.log(this.state.isLoading)
            var params = {
                entity_type_id: 3,
                email: this.state.email,
                password: this.state.password,
                first_name: this.state.first_name,
                last_name: this.state.last_name,
                mobile_no: this.state.mobile_no,
                is_auth_exists: IS_AUTH_EXISTS,
                has_temp_password: IS_TEMP_PASSWORD,
                conf_password: this.state.conf_password
            }
            
            var formData = new FormData();
            for (var k in params) {
                formData.append(k, params[k]);
            }
      
            if(Platform.OS == 'ios') {
              fetch("https://admin.ritehauler.com/api/entity_auth/signup_new",
                {
                    method:'POST',
                    body: formData,
                  }).then((response) => response.json())
                  .then((responseJson) => {
                    this.setState({ isLoading: false })
                    // responseJson.error == 1 ? alert(responseJson.message) : Actions.thankyou()
                      console.log(responseJson);
                      if(responseJson.error == 1) {
                        this.setState({ message : responseJson.message})
                        // alert(responseJson.message)
                        // Toast.show(responseJson.message)
                      } else {
                        // alert('Thank you for Signup')
                        // this.setState({ isLoading: false});
                        // Toast.show('Thank you for Signup')
                        this.setState({ message : 'Thank you for Signup'})
                        Actions.thankyou();
                      }
                  })
           } 
            
            if(Platform.OS == 'android') {
              fetch("https://admin.ritehauler.com/api/entity_auth/signup_new",
                {
                  method:'POST',
                  body: formData,
                }).then(response => response.text())
                .then(response => {
                  this.setState({ isLoading: false })
                  const result = response;
                  // this.setState({ isLoading: false});
                  // console.log(response);
                  // console.log(Object.values(result)[37])
                  // console.log(Object.values(result)[57])
                  // console.log(Object.values(result)[14])
  
                  if(Object.values(result)[37] == 0){
                    this.setState({ message : 'Thank you for Signup'})

                    // ToastAndroid.show('Thank you for Signup',ToastAndroid.SHORT)
                    Actions.thankyou();
                  }
                  if(Object.values(result)[57] == 1){ 
                    this.setState({ message : 'Email already exists'})
                    // ToastAndroid.show("Email already exists",ToastAndroid.SHORT)
                  }
                  if(Object.values(result)[14] == 1){
                    this.setState({ message : 'Password and Confirm Password does not match'})
                    // ToastAndroid.show("Password and Confirm Password does not match",ToastAndroid.SHORT)
                  } 
                })
            }
        // })
    }
      // if( helper.isValidEmail(this.emailInputSignUp.getText()) &&
      //       helper.isValidPassInput(this.passInput.getText()) &&
      //       helper.isFirstNameValid(this.firstNameInput.getText()) &&
      //       helper.isFirstNameValid(this.lastNameInput.getText()) &&
      //       helper.isValidMobilNumber(this.mobileNumberInput.getText()) 
      //     ){
      //         // console.log('if')
      //         this.setState({ isLoading: true})
      //         Keyboard.dismiss();
      //         //  this.setState({ isLoading: true }, () => {
      //         var params = {
      //           entity_type_id:3,
      //           email:this.emailInputSignUp.getText(),
      //           password:this.passInput.getText(),
      //           first_name:this.firstNameInput.getText(),
      //           last_name:this.lastNameInput.getText(),
      //           mobile_no:this.mobileNumberInput.getText(),
      //           is_auth_exists:IS_AUTH_EXISTS,
      //           has_temp_password:IS_TEMP_PASSWORD,
      //           conf_password:this.confirmPassInput.getText()
      //         }
      
      //       var formData = new FormData();
      //       for (var k in params) {
      //           formData.append(k, params[k]);
      //       }
      
      //       if(Platform.OS == 'ios') {
      //         fetch("https://admin.ritehauler.com/api/entity_auth/signup_new",
      //           {
      //               method:'POST',
      //               body: formData,
      //             }).then((response) => response.json())
      //             // response.text())
      //             .then((responseJson) => {
      //               // this.setState({ isLoading: false});
      //                 console.log(responseJson);

      //                 if(responseJson.error == 1) {
      //                   this.setState({ isLoading: false});
      //                   Toast.show(responseJson.message)
      //                 } else {
      //                   this.setState({ isLoading: false});
      //                   Toast.show('Thank you for Signup')
      //                   Actions.thankyou();
      //                 }
      //             })
      //             .catch((error => console.log(error)),
      //                 this.setState({isLoading: false})
      //             )
      //       } 
                
      //           if(Platform.OS == 'android') {
      //             // if(Platform.OS == 'android' && this.passInput.getText() !== this.confirmPassInput.getText()){
      //             //   this.setState({isLoading: false});
      //             //   ToastAndroid.show("Password and Confirm Password does not match.")
      //             // }   
      
      //               fetch("https://admin.ritehauler.com/api/entity_auth/signup_new",
      //               {
      //                 method:'POST',
      //                 body: formData,
      //               }).then(response => response.text())
      //               // response.text())
      //               .then(response => {
      //                 const result = response;
      //                 this.setState({ isLoading: false});

      //                  console.log(response);
      //                  console.log(Object.values(result)[37])
      //                  console.log(Object.values(result)[57])
      //                  console.log(Object.values(result)[14])
  
      //                 if(Object.values(result)[37] == 0){
      //                   // Alert.alert("Thank for Signup")
      //                   ToastAndroid.show('Thank you for Signup',ToastAndroid.SHORT)
      //                   Actions.thankyou();
      //                 }
      //                 if(Object.values(result)[57] == 1){ 
      //                   ToastAndroid.show("Email already exists",ToastAndroid.SHORT)
      //                 }
      //                 if(Object.values(result)[14] == 1){
      //                   ToastAndroid.show("Password and Confirm Password does not match",ToastAndroid.SHORT)
      //                 } 
      //               })
      //             }
              
      //                 // console.log(Object.values(result)[37])
      //               // //57
      //               // console.log(Object.values(result)[56])
      //               // //14
      //               // console.log(Object.values(result)[79])
      
      //               // if(Object.values(result)[37] === 0){
      //               //   alert("thank you")
      //               //   // Alert.alert("Thank for Signup")
      //               //   Toast.show('Thank you for Signup')
      //               //   Actions.thankyou();
      //               // }
      //               // if(Object.values(result)[56] === 1){ 
      //               //   Toast.show("Email already exists")
                      
      //               // }
      //               // if(Object.values(result)[79] === 1){ 
      //               //   alert("Password and Confirm Password does not match")
                    
      //               // }
      //         // })             
      //       }
      //       else{
      //         console.log('else')

      //         this.setState({ isLoading: false})

      //          //empty input
      //         //  let emptyFirst = helper.isEmptyValid(this.firstNameInput.getText());
      //         //  let emptyLast = helper.isEmptyValid(this.lastNameInput.getText());
      //         //  let emptyEmail = helper.isEmptyValid(this.emailInputSignUp.getText());
      //         //  let emptyNumber = helper.isEmptyValid(this.mobileNumberInput.getText());
      //           //Wrong input
      //         let validFirst =  helper.isFirstNameValid(this.firstNameInput.getText());
      //         let validLast = helper.isFirstNameValid(this.lastNameInput.getText());
      //         let validEmail = helper.isValidEmail(this.emailInputSignUp.getText());
      //         let validNumber = helper.isValidMobilNumber(this.mobileNumberInput.getText());
      //         let validPassword = helper.isValidConfirmPass(this.passInput.getText());
      //         let validConfirmPassword = helper.isValidConfirmPass(this.confirmPassInput.getText());

      //              //wrong input
      //             if(!validPassword){
      //               this.passInput.setError(true);
      //               this.passInput.focus();
      //             }
      //             if(!validConfirmPassword){
      //             this.confirmPassInput.setError(true);
      //             this.confirmPassInput.focus();
      //             }
      //              if(!validLast){
      //               this.lastNameInput.setError(true);
      //               this.lastNameInput.focus();
      //              }
      //              if(!validEmail){
      //               this.emailInputSignUp.setError(true);
      //               this.emailInputSignUp.focus();
      //              }
      //              if(!validNumber){
      //                this.mobileNumberInput.setError(true);
      //                this.mobileNumberInput.focus();
      //              }
      //              if(!validFirst){
      //               this.firstNameInput.setError(true);
      //               this.firstNameInput.focus();
      //            }
      //       }
   };
     
    renderFirstName() {
        return (
          // <View>
          <TextInput
            style={styles.textInput}
            returnKeyType="done"
            errorMessage="Enter a valid First Name "
            blurOnSubmit={false}
            value={this.state.first_name}
            onChangeText={(value)=>this.setState({first_name:value})} 
            ref={input => { this.first_name = input }}
            // onSubmitEditing={() => this.first_name.focus()}
            placeholder="First Name"
            placeholderTextColor='grey'
            autoCapitalize="none"
            onFocus={() => this.setState({ firstnameError: null })}
            />
        );
      }
      
      renderLastName() {
        return (
          <TextInput
            style={styles.textInput}
            returnKeyType="done"
            errorMessage="Enter a valid Last Name "
            blurOnSubmit={false}
            value={this.state.last_name}
            onChangeText={(value)=>this.setState({last_name:value})} 
             ref={input => { this.last_name = input }}
            // onSubmitEditing={() => this.lastNameInput.focus()}
            placeholder="Last Name"
            placeholderTextColor='grey'
            autoCapitalize="none"
            onFocus={() => this.setState({ lastnameError: null })}
          />
        );
      }

      renderEmail() {
        return (  
          <TextInput
            style={styles.textInput}
            returnKeyType="done"
            errorMessage="Enter a valid email address "
            blurOnSubmit={false}
            value={this.state.email}
            onChangeText={(value)=>this.setState({email:value})} 
            ref={input => { this.email = input }}
            // onSubmitEditing={() => this.lastNameInput.focus()}
            placeholder="Email address"
            placeholderTextColor='grey'
            autoCapitalize="none"
            keyboardType='email-address'
            onFocus={() => this.setState({ emailError: null })}

          />
        );
      }

      renderPhoneNumber() {
        return (
          <TextInput
            style={styles.textInput}
            returnKeyType="done"
            errorMessage="Number should be in format +1-0123456789 "
            blurOnSubmit={false}
            value={this.state.mobile_no}
            onChangeText={(value)=>this.setState({mobile_no:value})} 
            ref={input => { this.mobile_no = input }}
            placeholderTextColor='grey'
            // onSubmitEditing={() => this.lastNameInput.focus()}
            autoCapitalize="none"
            placeholder="+1 0821234567"
            onFocus={() => this.setState({ numberError: null })}
          />
        );
      }

      renderPassword() {
        return (
          <TextInput
            style={styles.textInput}
            returnKeyType="done"
            errorMessage="Password length must be greater then 6 "
            blurOnSubmit={false}
            value={this.state.password}
            onChangeText={(value)=>this.setState({password:value})} 
            ref={input => { this.password = input }}
            // onSubmitEditing={() => this.lastNameInput.focus()}
            placeholderTextColor='grey'
            autoCapitalize="none"
            placeholder="Password"
            secureTextEntry
            onFocus={() => this.setState({ passwordError: null })}
          />
        );
      }
      renderConfirmPassword() {
        return (
          <TextInput
            style={styles.textInput}
            returnKeyType="done"
            errorMessage="Password length must be greater then 6 "
            blurOnSubmit={false}
            value={this.state.conf_password}
            onChangeText={(value)=>this.setState({conf_password:value})} 
            ref={input => { this.conf_password = input }}
            // onSubmitEditing={() => this.lastNameInput.focus()}
            placeholderTextColor='grey'
            autoCapitalize="none"
            placeholder="Confirm Password"
            secureTextEntry
            onFocus={() => this.setState({ conf_passwordError: null })}
          />
        );
      }

     
    render(){
        return(
         <ScrollView style={styles.scrollStyle}>

         {/* <View> */}
            <View style={styles.container}>
            <Spinner visible={this.state.isLoading} textContent={''} color='red' />

              <View style={styles.eachBox}>
                    <View style={styles.leftContainer}>
                      {this.renderFirstName()}
                      {this.state.firstnameError && 
                        <Text style={styles.error}>{this.state.firstnameError}</Text>
                      }
                    </View>
                    <View style={styles.rightContainer}>
                      {this.renderLastName()} 
                      {this.state.lastnameError && 
                        <Text style={styles.error}>{this.state.lastnameError}</Text>
                      }
                    </View>                 
              </View>
           
            {this.renderEmail()}
                      {this.state.emailError && 
                        <Text style={styles.error}>{this.state.emailError}</Text>
                      }

            {this.renderPhoneNumber()}
                      {this.state.numberError && 
                        <Text style={styles.error}>{this.state.numberError}</Text>
                      }

            {this.renderPassword()}
                      {this.state.passwordError && 
                        <Text style={styles.error}>{this.state.passwordError}</Text>
                      }

            {this.renderConfirmPassword()}
                      {this.state.conf_passwordError && 
                        <Text style={styles.error}>{this.state.conf_passwordError}</Text>
                      }

              <View style={styles.buttonWrapper}>
                <BottomButton
                  title="Sign up"
                  style={styles.button}
                  onPress={()=>this.userSignUp()}
                />
              </View>
          <View style={styles.textView}>
            <Text style={styles.textStyle}>
                By Signing up,you confirm that you accept our
            </Text> 
            <Text style={styles.textStyle}>
                <Text style={styles.blackText}>Terms Of Use</Text> 
                {' '}
                and
                {' '}
                <Text style={styles.blackText}>Privacy Policy</Text>
            </Text>
          </View>

          {this.state.message
              && setTimeout(async () => {
                this.setState({ message: null })
              }, 1500)
            &&
            <View style={styles.errorView}>
              <Text style={styles.message}>{this.state.message}</Text>
            </View>}

        </View>


        <View style={styles.viewSign}>
            <Text style={styles.signText}>
                Already have an account? 
                {' '}
                {/* <TouchableOpacity > */}
                <Text style={styles.signTextTwo} onPress={this.login}>Sign in</Text>
                {/* </TouchableOpacity> */}
            </Text>
        </View>
    
     </ScrollView>
      );
    }
}
const mapStateToProps = ({ user }) => {
    const componentData = { ...user };
    return {
      componentData,
      modal: true
    };
  };
  const actions = { request };
  
  export default connect(mapStateToProps, actions)(WithLoader(Signup));
  