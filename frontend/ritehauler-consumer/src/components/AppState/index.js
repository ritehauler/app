import React from "react";
import PropTypes from "prop-types";
import { AppState as AppStateRN } from "react-native";

export default class AppState extends React.PureComponent {
  static propTypes = {
    handleAppState: PropTypes.func
  };

  static defaultProps = {
    handleAppState: () => {}
  };

  componentDidMount() {
    AppStateRN.addEventListener("change", this._handleAppStateChange);
  }

  componentWillUnmount() {
    AppStateRN.removeEventListener("change", this._handleAppStateChange);
  }

  getCurrentState = () => AppStateRN.currentState;

  _handleAppStateChange = nextAppState => {
    const { handleAppState } = this.props;
    if (handleAppState) {
      handleAppState(nextAppState);
    }
  };

  render() {
    return null;
  }
}
