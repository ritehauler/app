import React, { Component } from "react";
import { StyleSheet, View } from "react-native";
import { Actions } from "react-native-router-flux";
//import { View } from "react-native-animatable";
import _ from "lodash";
import LoadingRequest from "../../components/LoadingRequest";
import {
  Text,
  BottomButton,
  FloatLabelTextInput,
  Loading
} from "../../components";

const WithLoader = Child => {
  return class WithLoader extends Component {
    shouldComponentUpdate(nextProps, nextState) {
      if (Actions.currentScene !== this.props.navigation.state.routeName) {
        return false;
      }
      return true;
    }
    render() {
      const {
        componentData,
        modal,
        navigation,
        screenProps,
        titleStyle,
        headerStyle,
        headerTintColor,
        title,
        init,
        name,
        ...rest
      } = this.props;
      const filteredProps = { ...componentData, ...rest };

      if (!modal && componentData.isFetching && _.isEmpty(componentData.data)) {
        return <LoadingRequest />;
      }
      return (
        <View style={styles.container}>
          <Child {...filteredProps} />
          {modal && <Loading loading={componentData.isFetching} />}
        </View>
      );
    }
  };
};

export default WithLoader;

const styles = StyleSheet.create({
  container: {
    flex: 1
  }
});
